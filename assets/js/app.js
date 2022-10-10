(function($) {
    // trigger when login form is submitted
    $(document).on('submit', '#login', function () {

        // get form data
        var login_form = $(this);
        var form_data = JSON.stringify(login_form.serialize());
console.log(form_data);

        // http request here
        // submit form data to api
        $.ajax({
            url: 'https://scholarium.tmtg-clone.click/api/login',
            type: 'POST',
            mode: 'cors',
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
console.log(result);
                // // store jwt to cookie
                // setCookie("jwt", result.jwt, 1);

                // // show home page & tell the user it was a successful login
                // showHomePage();
                // $('#response').html("<div class='alert alert-success'>Successful login.</div>");

            },
            // error response will be here
            error: function (xhr, resp, text) {
    console.log(text);
                // // on error, tell the user login has failed & empty the input boxes
                // $('#response').html("<div class='alert alert-danger'>Login failed. Email or password is incorrect.</div>");
                // login_form.find('input').val('');
            }
        });

        return false;
    });

})(jQuery);
