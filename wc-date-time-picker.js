jQuery(document).ready(function($) {
  $("form.cart").on("submit", function(e) {
    var $form = $(this);
    var wcDate = $form.find("#wc-date-picker").val();
    var wcTime = $form.find("#wc-time-picker").val();

    if (!wcDate || !wcTime) {
      e.preventDefault();
      alert("Please enter a date and time before adding to the cart.");
      return false;
    }

    $("<input>")
      .attr("type", "hidden")
      .attr("name", "wc_date_time")
      .val(wcDate + " " + wcTime)
      .appendTo($form);
  });
});
