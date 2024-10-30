/**
 * Created by andrea on 02/10/14.
 */

(function ($) {
    var form$ = $('form[action*="://LOGIN_URL"]');
    if (form$.length != 1)
        form$ = $('form[action^="/LOGIN_PATH"]');
    if (form$.length != 1)
        form$ = $('iframe[src*="://LOGIN_URL"]').contents().find('form[action*="://LOGIN_URL"]');
    if (form$.length != 1)
        form$ = $('iframe[src^="/LOGIN_PATH"]').contents().find('form[action^="/LOGIN_PATH"]');
    if (form$.length != 1)
        return alert('This login dongle is for LOGIN_URL.');

    var challenge = 'CHALLENGE';
    var response = $.trim(prompt(challenge, ''));
    $('<input type="hidden">')
        .appendTo(form$)
        .attr('name', 'FIELD_NAME')
        .attr('value', response);
    $('[type="submit"]', form$)
        .click();
})(jQuery);
