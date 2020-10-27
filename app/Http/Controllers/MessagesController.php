<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Notifications\MessengerNew;
use Redirect;
use SEO;

class MessagesController extends Controller
{
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        // All threads that user is participating in
        $threads = Thread::forUser(Auth::id())->where('offer_id',NULL)->with(['participants','users','messages','participants.user'])->latest('updated_at')->get();

        // SEO Page Title
        SEO::setTitle(trans('messenger.messenger'). ' - ' . config('settings.page_name') . ' » ' . config('settings.sub_title'));


        if ($threads->isEmpty()) {
            return view('frontend.messenger.no-threads');
        } else {
            return view('frontend.messenger.index', ['threads' => $threads]);
        }
    }
    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        // Check if request was sent through ajax
        if (!request()->ajax()) {
            return redirect()->route('messages');
        }

        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('messages');
        }

        $userId = Auth::id();

        // Check if user is participant of this thread
        $participant = $thread->participants->where('user_id', $userId)->first();
        if (!isset($participant)) {
            return redirect()->route('messages');;
        }

        $messages = $thread->messages()->latest()->paginate(32);

        $thread->markAsRead($userId);
        return view('frontend.messenger.show', ['thread' => $thread, 'messages' => $messages]);
    }
    /**
     * Check for new messages in the thread
     *
     * @param $id
     * @return mixed
     */
    public function check($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
            return redirect()->route('messages');
        }

        return $thread->userUnreadMessagesCount(Auth::id());
    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store()
    {
        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        $input = Input::all();

        // Check if message is empty
        if (strlen(trim($input['message'])) == 0) {
            // Show alert
            \Alert::error('<i class="fa fa-times m-r-5"></i>' . trans('messenger.alert.no_input'))->flash();
            return back();
        }

        // Check if auth user is the recipient
        if (Auth::id() == $input['recipient']) {
          // Show alert
          \Alert::error('<i class="fa fa-times m-r-5"></i>' . trans('messenger.alert.self_message'))->flash();
          return back();
        }

        // Check if recipient exists
        $recipient = User::find($input['recipient']);
        if (!isset($recipient)) {
            // Show alert
            \Alert::error('<i class="fa fa-times m-r-5"></i>' . trans('messenger.alert.unkown_recipient'))->flash();
            return back();
        }

        // Check if thread already exists
        $thread = Thread::between([Auth::id(), $input['recipient']])->where('offer_id',NULL)->first();

        if (!isset($thread)) {
            $thread = Thread::create([
                'subject' => 'messenger',
            ]);
            // Sender
            Participant::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'last_read' => new Carbon,
            ]);
            // Recipients
            if (Input::has('recipient')) {
                $thread->addParticipant($input['recipient']);
            }
        } else {
            // Check if latest message contains same text (spam protection)
            $latest_message = $thread->latest_message;
            if (isset($latest_message) && $latest_message->created_at->addSeconds(10) >  \Carbon::now() && $latest_message->body == Input::get('message')) {
                // Show alert
                \Alert::error('<i class="fa fa-times m-r-5"></i>' . trans('messenger.alert.duplicate_message'))->flash();
                return redirect()->route('messages');
            }
        }
        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $input['message'],
        ]);

        // send notification to receiver
        $receiver_part = $thread->participants->where('user_id' ,'!=' ,Auth::id())->first();

        $receiver = User::find($receiver_part->user_id);

        $check_array = [
            'thread_id' => $thread->id,
            'user_id' => Auth::user()->id,
        ];

        // get latest thread notification for the user
        $notification_check = $receiver->notifications()->where('data', json_encode($check_array))->first();

        if (!$notification_check || !($notification_check->created_at->addMinutes('60') > \Carbon::now())) {
            $receiver->notify(new MessengerNew($thread, Auth::user()));
        }

        return redirect()->route('messages');
    }
    /**
     * Adds a new message to a current thread.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        // Check if request was sent through ajax
        if (!request()->ajax()) {
            return redirect()->route('messages');
        }

        // Check if user is logged in
        if (!(Auth::check())) {
            return redirect()->route('frontend.auth.login');
        }

        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
            return redirect()->route('messages');
        }
        $thread->activateAllParticipants();

        // Check if message is empty
        if (strlen(trim(Input::get('message'))) == 0) {
            abort(406, trans('messenger.alert.no_input'));
        }

        // Check if user is participant of this thread
        $participant = $thread->participants->where('user_id', Auth::id())->first();
        if (!isset($participant)) {
            return redirect()->route('messages');;
        }

        // Check if latest message contains same text (spam protection)
        $latest_message = $thread->latest_message;
        if (isset($latest_message) && $latest_message->created_at->addSeconds(10) >  \Carbon::now() && $latest_message->body == Input::get('message')) {
           abort(429, trans('messenger.alert.duplicate_message'));
        }

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => Input::get('message'),
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
        ]);
        $participant->last_read = new Carbon;
        $participant->save();

        // send notification to receiver
        $receiver_part = $thread->participants->where('user_id' ,'!=' ,Auth::id())->first();

        $receiver = User::find($receiver_part->user_id);

        $check_array = [
            'thread_id' => $thread->id,
            'user_id' => Auth::user()->id,
        ];

        // get latest thread notification for the user
        $notification_check = $receiver->notifications()->where('data', json_encode($check_array))->first();

        if (!$notification_check || !($notification_check->created_at->addMinutes('60') > \Carbon::now())) {
            $receiver->notify(new MessengerNew($thread, Auth::user()));
        }

        return $id;
    }
}
