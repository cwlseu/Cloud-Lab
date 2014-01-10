$(document).ready(function(){
      $(document).on("click", ".show-answer", function() {
            $(this).next().toggle("normal")
            if($(this).html()=='查看答案') {
                  $(this).html('隐藏答案')
            } else{
                  $(this).html('查看答案')
            }
      })
      $(document).on("click", ".exer-list label", function() {
            var checkmark = $(this).find('.checkmark')
            checkmark.width()==0 ? checkmark.animate({width:"22px"}) : checkmark.animate({width:"0px"})
      })
      $('.indicator-hide').click(function(){
            setTimeout(function(){
                  $('#carousel-indicators-pc').slideUp()
                  changeheight()
            },800)
      })
      $('.indicator-show').click(function(){
            setTimeout(function(){
                  $('#carousel-indicators-pc').slideDown()
            },800)
      })
      $('#pbtoggle').click(function(){
            if($('#pbtoggle span').html()=='显示流程'){
                  $('#carousel-indicators-pc').slideDown()
                  $('#pbtoggle span').html('隐藏流程')
                  $('#pbtoggle img').css('transform','rotate(405deg)')
            }else{
                  $('#carousel-indicators-pc').slideUp()
                  $('#pbtoggle span').html('显示流程')
                  $('#pbtoggle img').css('transform','rotate(0deg)')
            }
      })
      $(window).load(function(){
            changeheight()
      })
      $(window).resize(function(){
            changeheight()
            var str=parseInt($('li.ppt-list-active').attr('id').substring(9))-1
            $('#ppt-content').css('margin-left',-$('#ppt-content li').width()*str);
      })
      function changeheight(){
            var height = $(window).height() - $('#carousel-indicators-pc').height() - 60
            $('#ppt .tab-content').height(height)

            var width = $('#show-ppt-middle').height() / 3 * 4
            $('#show-ppt-middle').width(width)
            $('#show-ppt-middle li').width(width)

            var rwidth = $('#show-ppt').width() - $('#show-ppt-left').width() - $('#show-ppt-middle').width() - 15
            $('#show-ppt-right').width(rwidth)
      }

})