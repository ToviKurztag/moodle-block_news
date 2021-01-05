require(['jquery'], function($){
var sum = 0;
var numnews = $(".displaynew").length;
$('.displaynew').each(function(i, ele) {
    sum = sum + $(ele).height(); 
});
console.log(sum);
 duration = sum / 30;
 move = "-" + sum  +"px";

 edit = $('.edit').attr('id');
 total = duration + "s";
        $("#listnews").css({
            'position' : 'absolute',
            'animation-play-state' : 'running',
            'animation-name' : 'runnews',
            'animation-timing-function' : 'linear',
            'animation-iteration-count' : 'infinite',
         });
         $("<style>div#listnews:hover {animation-play-state : paused; }</style>").appendTo("head");
         $("#listnews").css('animation-duration', total);

         $("#listnews").mouseover(function() {
            $("#listnews").css('animation-play-state', 'paused');
          });
         $("#listnews").mouseout(function() {
            $("#listnews").css('animation-play-state', 'running');
          });
         if(edit == 0) {
            $("<style>@keyframes runnews { from {top: 200px;}  to {top: " + move + ";} }</style>").appendTo("head");
         }
});
