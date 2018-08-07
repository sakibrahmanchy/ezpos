@if ($breadcrumbs)
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$breadcrumb->last)
                <li>
                	<a href="{{ $breadcrumb->url }}">
                		@if (isset($breadcrumb->icon))
                			<i class="fa fa-{{ $breadcrumb->icon }}" aria-hidden="true"></i>
            			@endif
                		{{ $breadcrumb->title }}
	                </a>
	            </li>
            @else
                <li class="active">
                	@if (isset($breadcrumb->icon))
            			<i class="fa fa-{{ $breadcrumb->icon }}" aria-hidden="true"></i>
        			@endif
                	{{ $breadcrumb->title }}
                </li>
				@endif
        @endforeach
    </ol>
@endif