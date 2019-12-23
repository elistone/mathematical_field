(function ($, Drupal) {

  var initialized;

  function initHoverEffect() {
    if (initialized) {
      return;
    }
    initialized = true;

    findFieldsForResultHover();
  }


  function findFieldsForResultHover() {
    var $resultsFields = $('.panel-result');

    $resultsFields.each(function () {
      var $this = $(this);

      var $elem = $this.find('.panel__content');

      if ($elem.length > 0) {
        var hover = $elem.data('hover');
        var result = $elem.data('result');
        var calculation = $elem.data('calculation');

        if (hover === 1 && !isNaN(result)) {
          activateHover($elem, result, calculation);
        }
      }
    });
  }

  function activateHover($elm, result, cal) {
    var text = "<div class='hover-text'>(" + Drupal.t("Hover over the calculation for the result") + ")</div>";
    var calHtml = "<span class='hover-cal'>" + cal + "</span>" + text;
    var calResult = "<span class='hover-result'>" + result + "</span>" + text;

    $elm.html(calHtml);

    $elm.hover(
      function () {
        $elm.html(calResult);
      }, function () {
        $elm.html(calHtml);
      });
  }

  Drupal.behaviors.hoverForResult = {
    attach: function (context, settings) {
      initHoverEffect();
    }
  };

})(jQuery, Drupal);
