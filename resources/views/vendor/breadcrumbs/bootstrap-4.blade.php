@if ($breadcrumbs)
<div class="page-breadcrumbs-wrapper">
  <div class="page-breadcrumbs">
    <div class="page">
      	<ol itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
      		@foreach ($breadcrumbs as $breadcrumb)
      			@if ($breadcrumb->url && !$breadcrumb->last)
      				<li itemprop="itemListElement" itemscope="itemscope" itemtype="http://schema.org/ListItem" class="breadcrumb-item"><a itemprop="item" href="{{ $breadcrumb->url }}"><span itemprop="name">{{ $breadcrumb->title }}</span></a><meta itemprop="position" content="{{ $loop->iteration }}" /></li>
      			@else
      				<li itemprop="itemListElement" itemscope="itemscope" itemtype="http://schema.org/ListItem" class="breadcrumb-item active o-50"><a itemprop="item" href="{{ $breadcrumb->url }}"><span itemprop="name">{{ $breadcrumb->title }}</span></a><meta itemprop="position" content="{{ $loop->iteration }}" /></li>
      			@endif
      		@endforeach
      	</ol>
    </div>
  </div>
</div>
@endif
