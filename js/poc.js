

(function ($) {

  $(document).ready(function() {
    $('.poc-filter').select2();
  });

  if($(".hp_poc_rows").length > 0) {
    $("#poc-country-filter").change(function() {
        var selectedLocation = $(this).children("option:selected").val();
        $( ".hp_poc_row" ).removeClass('visible');
        if(selectedLocation.length > 1) {
          // filtered countries and push names to headline
          var names  = $( ".hp_poc_row" ).filter( '[data-country*="' + selectedLocation + '"], [data-area*="vertriebsreferent"]' ).addClass('visible').data('country');
          const namesArr = names.split(",");
          const titleArr = [];
          $.each(namesArr, function(index, value) {
            var countryTitle = $('#poc-country-filter option[value=' + value +']').text();
            titleArr.push(countryTitle);
          });

          // push county names to headline
          $(".hp_poc_area_headline .et_pb_text_inner").text('').text(titleArr.join(', ')).wrapInner("<h3 />");
        }
    });
  }

  $("#poc-city-filter").change(function() {
      var selectedLocation = $(this).children("option:selected").val();
      $( ".hp_poc_row" ).removeClass('visible');

      if(selectedLocation.length > 1) {
          $( ".hp_poc_row" ).filter( '[data-city="' + selectedLocation + '"]' ).addClass('visible');
      }
  });

  // person ajax search
 $( ".search-autocomplete" ).autocomplete({
    minLength: 3,
    source: function( request, response ) {
     // Fetch data
     $.post(global.ajax, { search: request.term, action: 'search_site' }, function(res) {
       response(res.data);
     });

    },
    select: function (event, ui) {
     // Set selection
     $('.search-autocomplete').val(ui.item.label); // display the selected text

     var selectedName = ui.item.label.toLowerCase();
     $( ".hp_poc_row" ).removeClass('visible');
     if(selectedName.length > 1) {
       $( ".hp_poc_row" ).filter( '[data-name="' + selectedName + '"]' ).addClass('visible');
     }

     return false;
   },
   focus: function( event, ui ) {
     var selectedName = ui.item.label.toLowerCase();
     $( ".hp_poc_row" ).removeClass('visible');
     if(selectedName.length > 1) {
       $( ".hp_poc_row" ).filter( '[data-name="' + selectedName + '"]' ).addClass('visible');
     }

     return false;
   }
 });

  // find person
  $('.search-autocomplete').keyup(delay(function (e) {
    var poc_input_value = $('.search-autocomplete').val();
    var poc_input_length = poc_input_value.length;

    $( ".hp_poc_row" ).removeClass('visible');
    if(poc_input_length > 2) {
      $( ".hp_poc_row" ).filter( '[data-name*="' + poc_input_value + '"]' ).addClass('visible');
    }
  }, 500));

  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }



})(jQuery);
