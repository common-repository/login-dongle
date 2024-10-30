/**
 * Created by andrea on 02/10/14.
 */

(function ($) {
    var form$ = $('form[action*="://local.wordpress.dev/wp-login.php"]');
    if (form$.length != 1)
        form$ = $('form[action^="/wp-login.php"]');
    if (form$.length != 1)
        form$ = $('iframe[src*="://LOGIN_URL"]').contents().find('form[action*="://LOGIN_URL"]');
    if (form$.length != 1)
        form$ = $('iframe[src^="/LOGIN_PATH"]').contents().find('form[action^="/LOGIN_PATH"]');
    if (form$.length != 1)
        return alert('This login dongle is for local.wordpress.dev/wp-login.php.');

    var challenge = 'Tell me your name.';
    var response = $.trim(prompt(challenge, ''));
    $('<input type="hidden">')
        .appendTo(form$)
        .attr('name', 'logindongle_Tell%20me%20your%20name%2E')
        .attr('value', response);
    $('[type="submit"]', form$)
        .click()
})(jQuery);
