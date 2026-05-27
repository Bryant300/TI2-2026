/* ============================================================================
   TRAVAIL D'INTÉGRATION JAVASCRIPT / jQuery
   Gestion d'un formulaire de contact + Dark Mode
   ============================================================================

   OBJECTIF GÉNÉRAL
   ----------------
   Créer une page contenant un formulaire de contact validé côté client en
   jQuery, avec un système de bascule entre mode clair et mode sombre.
   L'envoi final est géré par PHP qui affiche un message de retour.

   ============================================================================
   PARTIE 1 — STRUCTURE HTML À PRÉVOIR
   ============================================================================

   Vous devez créer un formulaire contenant AU MINIMUM les champs suivants :

     - Nom               (input text)
     - Prénom            (input text)
     - Email             (input email)
     - Code postal belge (input text)
     - Numéro de téléphone belge (input text)
     - Message           (textarea)
     - Bouton d'envoi    (button submit)

   Prévoir également :
     - Une zone <div id="messages"></div> en HAUT du formulaire pour afficher
       les messages d'erreur (rouge) ou de succès (vert).
     - Un bouton <button id="toggle-theme"></button> pour basculer le thème.

   ============================================================================
   PARTIE 2 — VALIDATION JAVASCRIPT (jQuery OBLIGATOIRE)
   ============================================================================

   Au clic sur le bouton d'envoi, vérifier CHAQUE champ.
   Si un champ ne respecte pas sa condition, afficher un message EN ROUGE
   en haut du formulaire, dans la zone #messages.
   Si TOUS les champs sont valides, afficher un message EN VERT et envoyer
   le formulaire (qui sera traité par PHP — voir partie 3).

   --- RÈGLES DE VALIDATION ---

   1) Nom et Prénom
      - Champs obligatoires (non vides)
      - Au moins 2 caractères

   2) Email
      - Champ obligatoire
      - Doit respecter le format d'une adresse email valide
        (utiliser une expression régulière — regex)

   3) Code postal belge
      - 4 chiffres exactement
      - Compris entre 1000 et 9999

   4) Numéro de téléphone belge
      - Doit accepter les formats suivants :
          • 0470123456
          • 0470 12 34 56
          • +32 470 12 34 56
          • 0032470123456
      - Indice : nettoyer la chaîne (enlever espaces, tirets, points)
        AVANT de tester avec une regex

   5) Message
      - Champ obligatoire
      - Au moins 10 caractères

   --- AFFICHAGE DES MESSAGES ---

   - Tous les messages d'erreur s'affichent dans la zone #messages,
     en haut du formulaire.
   - Couleur rouge pour les erreurs, couleur verte pour le succès.
   - Vider la zone à chaque nouvelle tentative d'envoi.

   ============================================================================
   PARTIE 3 — TRAITEMENT CÔTÉ PHP
   ============================================================================

   Si tous les champs sont valides, le formulaire est envoyé à un script PHP.
   Ce script doit afficher :

     - "Merci pour votre nouveau message" en VERT si l'envoi a réussi.
     - "Problème lors de l'envoi du message" en ROUGE si l'envoi a échoué.

   Note : pour cet exercice, le PHP peut simuler la réussite/échec
   (par exemple, vérifier que les variables $_POST sont bien remplies).

   ============================================================================
   PARTIE 4 — DARK MODE
   ============================================================================

   Créer un bouton qui permet de basculer entre deux thèmes :

     ☀️ Mode clair  → body avec fond BLANC
     🌙 Mode sombre → body avec fond NOIR

   COMPORTEMENT DU BOUTON :
   - Le texte du bouton change dynamiquement :
       • "🌙 Dark Mode"  quand on est en mode clair (clic = passer en sombre)
       • "☀️ White Mode" quand on est en mode sombre (clic = passer en clair)
   - L'icône doit correspondre au mode vers lequel on bascule.

   IMPLÉMENTATION SUGGÉRÉE :
   - Utiliser une classe CSS (ex : .dark-mode) sur le <body>.
   - Faire le toggle de cette classe en jQuery avec .toggleClass().
   - Mettre à jour le texte du bouton après chaque toggle.

   ============================================================================
   PARTIE 5 — BONUS
   ============================================================================

   Sur le champ "Message", limiter dynamiquement à 300 caractères MAXIMUM.

   Suggestions :
   - Utiliser l'attribut HTML maxlength="300" (rapide mais peu visuel)
   - OU mieux : afficher un compteur en temps réel sous le champ,
     du type "143 / 300 caractères", qui se met à jour à chaque frappe.
   - Bonus du bonus : passer le compteur en rouge quand il approche
     de la limite (par exemple à partir de 280 caractères).

   ============================================================================
   CRITÈRES D'ÉVALUATION
   ============================================================================

   - Utilisation correcte de jQuery (sélecteurs, événements, manipulation DOM)
   - Validation rigoureuse de tous les champs avec les bonnes regex
   - Affichage clair des messages d'erreur et de succès
   - Dark mode fonctionnel avec changement dynamique du texte/icône
   - Code propre, indenté et commenté
   - HTML sémantique et CSS soigné
   - Bonus implémenté (compteur de caractères)

   ============================================================================
   À RENDRE
   ============================================================================

   - script.js   (toute la logique jQuery)
   - traitement.php

   Bon travail !
   ========================================================================= */
/* ============================================================================
   validation.js
   Gestion du formulaire livre d'or + Dark Mode + Compteur de caractères
   jQuery obligatoire
   ============================================================================ */

$(document).ready(function () {
  $("#toggle-theme").on("click", function () {
    $("body").toggleClass("dark-mode");

    if ($("body").hasClass("dark-mode")) {
      // On est maintenant en mode sombre → proposer de revenir au clair
      $(this).text("Light Mode");
    } else {
      // On est en mode clair → proposer de passer en sombre
      $(this).text("Dark Mode");
    }
  });

  const MAX_CHARS = 300;
  const WARN_LIMIT = 280; // Seuil à partir duquel le compteur passe en rouge

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
    if (value.trim() === "") return true; // Champ optionnel
    var code = parseInt(value.trim(), 10);
    return /^\d{4}$/.test(value.trim()) && code >= 1000 && code <= 9999;
  }

  function validatePhone(value) {
    if (value.trim() === "") return true; // Champ optionnel

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
