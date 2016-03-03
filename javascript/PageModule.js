(function($) {

  $.entwine('ss', function($) {

    $('#NewClassName li').entwine({
      onclick: function(e) {
        e.preventDefault();
        
        $('#NewClassName li input').prop('checked', false);
        $(this).find('input').prop('checked', true);

        $('#NewClassName li').removeClass('active');
        $(this).addClass('active');
      }
    });

  });

}(jQuery));