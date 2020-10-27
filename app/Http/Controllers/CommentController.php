<?php
namespace App\Http\Controllers;

use Request;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Listing;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator, Redirect, Session, Theme;
use App\Notifications\ListingCommentNew;

class CommentController extends Controller
{

    /**
     * Show comments.
     *
     * @param  string  $type, int  $type_id
     * @return view
     */
    public function show($type, $type_id)
    {
        // Get used model type
        switch ($type) {
            case 'game':
                $type = 'App\Models\Game';
                break;
            case 'listing':
                $type = 'App\Models\Listing';
                break;
            case 'article':
                $type = 'App\Models\Article';
                break;
        }

        // check for ajax requet - block everything else
        if (!Request::ajax()) {
            return abort('404');
        }

        // don't loose backUrl session if one is set
        if (Session::has('backUrl')) {
            Session::keep('backUrl');
        }

        // Get all comments for this item
        $comments = Comment::with('user')
                            ->where('commentable_id', $type_id)
                            ->where('commentable_type', $type)
                            ->where('root_id', 0)
                            ->where('status', 1)
                            ->orderBy('created_at','asc')
                            ->paginate(config('settings.comment_max_page'));

        return view('frontend.comments.show', ['comments' => $comments]);
    }

    /**
     * Show comment likes.
     *
     * @param  int  $id
     * @return view
     */
    public function likes($id)
    {

        // check for ajax requet - block everything else
        if (!Request::ajax()) {
            return abort('404');
        }

        // Get all comments for this item
        $likes = CommentLike::with('user')
                          ->where('comment_id', $id)
                          ->orderBy('created_at','asc')
                          ->get();

        return view('frontend.comments.likes', ['likes' => $likes]);
    }

    /**
     * Post new comment.
     *
     * @return mixed
     */
    public function post()
    {

        // check for ajax requet - block everything else
        if (!Request::ajax()) {
            return abort('404');
        }

        // Check if user is logged in
        if (!(\Auth::check())) {
            return response(['error' => 'login'], 303);
        }

        // check if user account is active
        if (!\Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        // Throttle protection
        $last_user_comment = Comment::where('user_id', \Auth::id())  ->orderBy('created_at','desc')->first();

        if ($last_user_comment && $last_user_comment->created_at->addSeconds(config('settings.comment_throttle')) > \Carbon::now()) {
            return response(['error' => 'throttle'], 303);
        }


        $data = Input::all();

        // Get used model type
        switch ($data['item_type']) {
            case 'game':
                $type = 'App\Models\Game';
                break;
            case 'listing':
                $type = 'App\Models\Listing';
                break;
            case 'article':
                $type = 'App\Models\Article';
                break;
        }

        // Check for text input
        if ($data['text'] == '') {
            return response(['error' => 'no_input'], 303);
        }

        $comment = new Comment;

        $comment->content = $data['text'];
        $comment->user_id = \Auth::id();
        $comment->commentable_id = $data['item_id'];
        $comment->commentable_type = $type;
        $comment->status = 1;
        $comment->root_id = 0;

        $comment->save();

        // Send notification (only listing comments)
        if ($data['item_type'] == 'listing') {
            $listing = Listing::findOrFail($data['item_id']);
            $listing_user = User::find($listing->user_id);

            // Check if comment is not from listing user
            if ($listing_user->id != $comment->user_id) {
                $listing_user->notify(new ListingCommentNew($comment, $listing));
            }
        }

        // Get all comments for this item
        $comments = Comment::where('commentable_id',$data['item_id'])
                            ->where('commentable_type',$type)
                            ->where('root_id', 0)
                            ->where('status', 1)
                            ->orderBy('created_at','asc')->paginate(config('settings.comment_max_page'));

        // return last page
        return url('comments/show/'.$data['item_type'].'/'.$data['item_id'].'?page='.$comments->lastPage() );

    }


    /**
     * Post reply.
     *
     * @return mixed
     */
    public function postReply()
    {
        $data = Input::all();

        // check for ajax requet - block everything else
        if (!Request::ajax()) {
            return abort('404');
        }

        // Check if user is logged in
        if (!(\Auth::check())) {
            return response(['error' => 'login'], 303);
        }

        // check if user account is active
        if (!\Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        // Throttle protection
        $last_user_comment = Comment::where('user_id', \Auth::id())  ->orderBy('created_at','desc')->first();

        if ($last_user_comment && $last_user_comment->created_at->addSeconds(config('settings.comment_throttle')) > \Carbon::now()) {
            return response(['error' => 'throttle'], 303);
        }

        // Check for text input
        if ($data['replyText'] == '') {
            return response(['error' => 'no_input'], 303);
        }

        // get root comment
        $root = Comment::where('id', $data['parent_id'])->first();

        // create reply comment
        $reply = new Comment;

        $reply->content = $data['replyText'];
        $reply->commentable_type = $root->commentable_type;
        $reply->commentable_id = $root->commentable_id;
        $reply->user_id = \Auth::id();
        $reply->root_id = $data['parent_id'];

        $reply->save();

        // save that root has childrens
        $root->has_children = 1;
        $root->save();

        // return current page url for ajax refresh
        return url('comments/show/'.$root->type.'/'.$root->commentable_id.'?page='.$data['current_page']  );

    }


    /**
     * Like comment.
     *
     * @return mixed
     */
    public function like()
    {

        // check for ajax requet - block everything else
        if (!Request::ajax()) {
            return abort('404');
        }

        // Check if user is logged in
        if (!(\Auth::check())) {
            return response(['error' => 'login'], 303);
        }

        // check if user account is active
        if (!\Auth::user()->isActive()) {
            \Auth::logout();
            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        $data = Input::all();

        $likecheck = CommentLike::where('comment_id',$data['id'])->where('user_id', \Auth::id())->first();

        $comment = Comment::find($data['id']);

        // check if no like exist
        if (is_null($likecheck) ){
            // create new like
            $like = new CommentLike;

            $like->user_id = \Auth::id();
            $like->comment_id = $data['id'];

            $like->save();

            // increment comment likes
            $comment->increment('likes');

            return $comment->likes;
        } else {
            // delete like
            $likecheck->forceDelete();

            // decrement comment likes
            $comment->decrement('likes');

            return $comment->likes;

        }
    }

    /**
     * Delete comment
     *
     * @param  int  $id, int  $page
     * @return view
     */
    public function delete($id , $page)
    {
        // Check if user is logged in
        if (!(\Auth::check())) {
            return abort('404');
        }

        // Check if user can delete comments
        if (!\Auth::user()->can('edit_comments')) {
            return abort('404');
        }

        $comment = Comment::where('id', $id)->first();

        // Check if comment exist
        if (!$comment) {
            return abort('404');
        }

        // return url
        $return_url = url('comments/show/' . $comment->type . '/' . $comment->commentable_id . '?page=' . $page);

        // delete comment likes
        foreach ($comment->dblikes as $comment_like) {
          $comment_like->delete();
        }

        // delete comment
        $comment->delete();

        // delete all child comments
        $child_comments = Comment::where('root_id', $comment->id)->get();

        foreach ($child_comments as $child_comment) {
            // delete child comment likes
            foreach ($child_comment->dblikes as $child_comment_like) {
              $child_comment_like->delete();
            }
            $child_comment->delete();
        }

        return $return_url;
    }
}
