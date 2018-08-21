$(document).ready(function(){


    updateTextFields = function() {
        var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';
        $(input_selector).each(function(index, element) {
            if ($(element).val().length > 0 || element.autofocus ||$(this).attr('placeholder') !== undefined || $(element)[0].validity.badInput === true) {
                $(this).siblings('label, i').addClass('active');
            }
            else {
                $(this).siblings('label, i').removeClass('active');
            }
        });
    };

    // Text based inputs
    var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';

    // Add active if form auto complete
    $(document).on('change', input_selector, function () {
        if($(this).val().length !== 0 || $(this).attr('placeholder') !== undefined) {
            $(this).siblings('label').addClass('active');
        }
        validate_field($(this));
    });

    // Add active if input element has been pre-populated on document ready
    $(document).ready(function() {
        updateTextFields();
    });

    // HTML DOM FORM RESET handling
    $(document).on('reset', function(e) {
        var formReset = $(e.target);
        if (formReset.is('form')) {
            formReset.find(input_selector).removeClass('valid').removeClass('invalid');
            formReset.find(input_selector).each(function () {
                if ($(this).attr('value') === '') {
                    $(this).siblings('label, i').removeClass('active');
                }
            });

            // Reset select
            formReset.find('select.initialized').each(function () {
                var reset_text = formReset.find('option[selected]').text();
                formReset.siblings('input.select-dropdown').val(reset_text);
            });
        }
    });

    // Add active when element has focus
    $(document).on('focus', input_selector, function () {
        $(this).siblings('label, i').addClass('active');
    });

    $(document).on('blur', input_selector, function () {
        var $inputElement = $(this);
        if ($inputElement.val().length === 0 && $inputElement[0].validity.badInput !== true && $inputElement.attr('placeholder') === undefined) {
            $inputElement.siblings('label, i').removeClass('active');
        }

        if ($inputElement.val().length === 0 && $inputElement[0].validity.badInput !== true && $inputElement.attr('placeholder') !== undefined) {
            $inputElement.siblings('i').removeClass('active');
        }
        validate_field($inputElement);
    });

    window.validate_field = function(object) {
        var hasLength = object.attr('length') !== undefined;
        var lenAttr = parseInt(object.attr('length'));
        var len = object.val().length;

        if (object.val().length === 0 && object[0].validity.badInput === false) {
            if (object.hasClass('validate')) {
                object.removeClass('valid');
                object.removeClass('invalid');
            }
        }
        else {
            if (object.hasClass('validate')) {
                // Check for character counter attributes
                if ((object.is(':valid') && hasLength && (len <= lenAttr)) || (object.is(':valid') && !hasLength)) {
                    object.removeClass('invalid');
                    object.addClass('valid');
                }
                else {
                    object.removeClass('valid');
                    object.addClass('invalid');
                }
            }
        }
    };

    // Radio and Checkbox focus class
    var radio_checkbox = 'input[type=radio], input[type=checkbox]';
    $(document).on('keyup.radio', radio_checkbox, function(e) {
        // TAB, check if tabbing to radio or checkbox.
        if (e.which === 9) {
            $(this).addClass('tabbed');
            var $this = $(this);
            $this.one('blur', function(e) {

                $(this).removeClass('tabbed');
            });
            return;
        }
    });

    // will replace .form-g class when referenced
    var material = '<div class="input-field col input-g s12">' +
        '<input name="option_name[]" id="option_name[]" type="text">' +
        '<span style="float:right; cursor:pointer;"class="delete-option">Delete</span>' +
        '<label for="option_name">Options</label>' +
        '<span class="add-option" style="cursor:pointer;">Add Another</span>' +
        '</div>';

    // for adding new option
    $(document).on('click', '.add-option', function() {
        $(".form-g").append(material);
    });
    // allow for more options if radio or checkbox is enabled
    $(document).on('change', '#question_type', function() {
        var selected_option = $('#question_type :selected').val();
        if (selected_option === "radio" || selected_option === "checkbox") {
            $(".form-g").html(material);
        } else {
            $(".input-g").remove();
        }
    });

    $(document).on('click', '.delete-option', function() {
        $(this).parent(".input-field").remove();
    });
});