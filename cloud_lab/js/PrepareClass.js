$(document).ready(function(){
      $(".show-answer").click(function(){
            $(this).next().toggle("normal")
            ($(this).html()=='查看答案') ? ($(this).html('隐藏答案')) : ($(this).html('查看答案'))
      })
      $('.exer-list label').click(function(){
            var checkmark = $(this).find('.checkmark')
            checkmark.width()==0 ? checkmark.animate({width:"22px"}) : checkmark.animate({width:"0px"})
      })
      $('.indicator-hide').click(function(){
            setTimeout(function(){$('#carousel-indicators-pc').slideUp()},500)
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
      })
      function changeheight(){
            var height = $(window).height() - $('#carousel-indicators-pc').height() - 70
            $('#ppt .tab-content').height(height)

            var width = $('#show-ppt-middle').height() / 3 * 4
            $('#show-ppt-middle').width(width)
            $('#show-ppt-middle li').width(width)
      }
})