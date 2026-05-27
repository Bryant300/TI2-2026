$(document).ready(function () {
  $("#toggle-theme").on("click", function () {
    $("body").toggleClass("dark-mode");

    if ($("body").hasClass("dark-mode")) {
      $(this).text("Light Mode");
    } else {
      $(this).text("Dark Mode");
    }
  });

  const MAX_CHARS = 300;
  const WARN_LIMIT = 280;
  $("#message").on("input", function () {
    const current = $(this).val().length;
    const $counter = $("#char-counter");

    $counter.text(current + " / " + MAX_CHARS + " caractères");

    if (current >= WARN_LIMIT) {
      $counter.addClass("danger");
    } else {
      $counter.removeClass("danger");
    }
  });

  function showMessage(text, type) {
    $("#messages").removeClass("error success").addClass(type).html(text);
  }

  function clearMessages() {
    $("#messages").removeClass("error success").html("");
  }

  function validateName(value) {
    return value.trim().length >= 2;
  }

  function validateEmail(value) {
    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    return regex.test(value.trim());
  }

  function validatePostcode(value) {
    if (value.trim() === "") return true;
    var code = parseInt(value.trim(), 10);
    return /^\d{4}$/.test(value.trim()) && code >= 1000 && code <= 9999;
  }

  function validatePhone(value) {
    if (value.trim() === "") return true;

    var cleaned = value.replace(/[\s\-\.]/g, "");

    var regex = /^(0\d{9}|\+32\d{9}|0032\d{9})$/;
    return regex.test(cleaned);
  }

  function validateMessage(value) {
    return value.trim().length >= 10;
  }

  $("#guestbook-form").on("submit", function (e) {
    e.preventDefault();

    clearMessages();

    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var usermail = $("#usermail").val();
    var phone = $("#phone").val();
    var postcode = $("#postcode").val();
    var message = $("#message").val();

    var errors = [];

    if (!validateName(firstname)) {
      errors.push(
        "Le prénom est obligatoire et doit contenir au moins 2 caractères.",
      );
    }

    if (!validateName(lastname)) {
      errors.push(
        "Le nom est obligatoire et doit contenir au moins 2 caractères.",
      );
    }

    if (usermail.trim() === "") {
      errors.push("L'adresse email est obligatoire.");
    } else if (!validateEmail(usermail)) {
      errors.push("L'adresse email n'est pas valide.");
    }

    if (!validatePostcode(postcode)) {
      errors.push(
        "Le code postal doit être composé de 4 chiffres et être compris entre 1000 et 9999.",
      );
    }

    if (!validatePhone(phone)) {
      errors.push(
        "Le numéro de téléphone belge n'est pas valide (ex : 0470 12 34 56 ou +32 470 12 34 56).",
      );
    }

    if (message.trim() === "") {
      errors.push("Le message est obligatoire.");
    } else if (!validateMessage(message)) {
      errors.push("Le message doit contenir au moins 10 caractères.");
    }

    if (errors.length > 0) {
      var errorHtml = "<ul>";
      $.each(errors, function (index, error) {
        errorHtml += "<li>" + error + "</li>";
      });
      errorHtml += "</ul>";
      showMessage(errorHtml, "error");

      $("html, body").animate(
        { scrollTop: $("#messages").offset().top - 20 },
        300,
      );
    } else {
      showMessage("Données valides — envoi en cours…", "success");

      setTimeout(function () {
        $("#guestbook-form").off("submit").submit();
      }, 600);
    }
  });
});
