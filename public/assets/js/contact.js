function uploadEnd(arg,instance) {
    jQuery('#'+instance+'contact-result').empty().append(arg + "<br>Mail sent : We'll answer as soon as possible.");
}
jQuery(document).ready(function ($) {

    message = "Loading...";
    isChange = false;

    $("input:file").change(function () {
        isChange = true
    });

    $(".ticket-submit").click(function (e) {
        var instance=e.target.form.id;
        e.preventDefault();

        var name = $('#' +instance + ' .message-name').val();
        var subject = $('#' +instance + ' .message-subject').val();
        var email = $('#' +instance + ' .message-email').val();
        var content = $('#' +instance + ' .message-content').val();
        var division = $('#' +instance + ' .message-division').val();
        var priority = $('#' +instance + ' .message-priority').val();
        var phone = $('#' +instance + ' .message-phone').val();
        var product = $('#' +instance + ' .message-product').val();
        var ticketid = $('#' +instance + " input#ticketid").val();
        var challengeField = $('#' +instance + " input#recaptcha_challenge_field").val();
        var responseField = $('#' +instance + " input#recaptcha_response_field").val();
        var fileField = $('#' +instance + " input#uploadFile").val();


        $('#'+instance+ 'contact-init').slideUp();
        $('#'+instance+ 'contact-loader').slideDown();
        $('#'+instance+ 'contact-result').empty().append("Loading...");

        $('#'+instance+ 'contact-result').slideDown();

        var  url = '';

        $.post(url, {action:'ticketsys-submit',name: name, subject: subject, email: email, content: content, division: division, priority: priority, phone: phone, product: product, ticketid: ticketid, challengeField: challengeField, responseField: responseField}, function (data) {
            switch (data[0]) {
                case -1:
                    var message = "Error. Please refresh the page or try again later.";
                    setTimeout(function () {
                        $('#'+instance+ 'contact-init').slideDown();
                        $('#'+instance+ 'contact-result').slideUp();
                    }, 4000);
                    break;

                case 0:
                    var message = "Mail not sent : Some informations are lacking.";
                    message += "<br>Inputs to fill : ";

                    if ($('#'+instance+ ' .message-name').val() == "") {
                        message += "name ";
                    }

                    if ($('#'+instance+ ' .message-subject').val() == "") {
                        message += "subject ";
                    }

                    if ($('#'+instance+ ' .message-email').val() == "") {
                        message += "email ";
                    }

                    if ($('#'+instance+ ' .message-content').val() == "") {
                        message += "content ";
                    }

                    if ($('#'+instance+  ' .message-division option:selected').val() == "Division") {
                        message += "division ";
                    }

                    if ($('#'+instance+  ' .message-priority option:selected').val() == "Priority") {
                        message += "priority ";
                    }

                    if ($('#'+instance+ ' .message-product option:selected').val() == "Product") {
                        message += "product ";
                    }


                    setTimeout(function () {
                        $('#'+instance+ 'contact-init').slideDown();
                        $('#'+instance+ 'contact-result').slideUp();
                    }, 4000);
                    break;

                case 1:
                    var message = "Mail not sent : Invalid email address.";
                    setTimeout(function () {
                        $('#'+instance+ 'contact-init').slideDown();
                        $('#'+instance+ 'contact-result').slideUp();
                    }, 4000);
                    break;

                case 2:

                    if (isChange) {
                        setTimeout(function () {
                            $('#'+instance+ 'contact-result').empty().append("Uploading your attachment...");
                        }, 1000);

                        $("#"+instance).submit();
                    }
                    else {
                        var message = "Mail sent : We'll answer as soon as possible.";
                    }

                    break;

                case 3:
                    var message = "The captcha wasn't entered or is incorrect.";
                     setTimeout(function () {
                        $('#'+instance+ 'contact-init').slideDown();
                        $('#'+instance+ 'contact-result').slideUp();
                    }, 4000);
                    break;

            }

            setTimeout(function () {
                $('#'+instance+ 'contact-loader').slideUp();
                $('#'+instance+ 'contact-result').empty().append(message);
            }, 2000);

        }, "json");

    });

});
