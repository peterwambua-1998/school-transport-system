// npm package: select2
// github link: https://github.com/select2/select2

$(function() {
  'use strict'

  if ($(".js-example-basic-single").length) {
    $(".js-example-basic-single").select2();
  }
  if ($(".js-example-basic-multiple").length) {
    $(".js-example-basic-multiple").select2();
  }
  if ($(".js-example-basic").length) {
    $(".js-example-basic").select2();
  }

  if ($(".js-example-basic-multiple2").length) {
    $(".js-example-basic-multiple2").select2();
  }
});