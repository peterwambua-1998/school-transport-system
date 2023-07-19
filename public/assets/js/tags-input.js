// npm package: jquery-tags-input
// github link: https://github.com/xoxco/jQuery-Tags-Input

$(function() {
  'use strict';

  $('#tags').tagsInput({
    'width': '100%',
    'height': '65%',
    'interactive': true,
    'defaultText': 'add ',
    'removeWithBackspace': true,
    'minChars': 0,
    'maxChars': 100,
    'placeholderColor': '#666666'
  });
});