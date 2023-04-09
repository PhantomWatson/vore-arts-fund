<?php
return [
    'nextActive' => '<li class="page-item next"><a rel="next" href="{{url}}"><i class="fa-solid fa-arrow-right"></i></a></li>',
    'nextDisabled' => '<li class="page-item next disabled"><a href="" onclick="return false;"><i class="fa-solid fa-arrow-right"></i></a></li>',
    'prevActive' => '<li class="page-item prev"><a rel="prev" href="{{url}}"><i class="fa-solid fa-arrow-left"></i></a></li>',
    'prevDisabled' => '<li class="page-item prev disabled"><a href="" onclick="return false;"><i class="fa-solid fa-arrow-left"></i></a></li>',
    'counterRange' => '{{start}} - {{end}} of {{count}}',
    'counterPages' => '{{page}} of {{pages}}',
    'first' => '<li class="page-item first"><a href="{{url}}">{{text}}</a></li>',
    'last' => '<li class="page-item last"><a href="{{url}}">{{text}}</a></li>',
    'number' => '<li class="page-item"><a href="{{url}}">{{text}}</a></li>',
    'current' => '<li class="page-item active"><a href="">{{text}}</a></li>',
    'ellipsis' => '<li class="page-item ellipsis">&hellip;</li>',
    'sort' => '<a href="{{url}}">{{text}}</a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}}</a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}}</a>',
    'sortAscLocked' => '<a class="asc locked" href="{{url}}">{{text}}</a>',
    'sortDescLocked' => '<a class="desc locked" href="{{url}}">{{text}}</a>',
];
