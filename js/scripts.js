/*!
    * Start Bootstrap - Creative v6.0.3 (https://startbootstrap.com/themes/creative)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-creative/blob/master/LICENSE)
    */
    (function($) {
  if (!$) return; // Skip if jQuery not available
  "use strict";

  // Smooth scrolling using jQuery easing
  $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: (target.offset().top - 72)
        }, 1000, "easeInOutExpo");
        return false;
      }
    }
  });

  // Closes responsive menu when a scroll trigger link is clicked
  $('.js-scroll-trigger').click(function() {
    $('.navbar-collapse').collapse('hide');
  });

  // Activate scrollspy to add active class to navbar items on scroll
  $('body').scrollspy({
    target: '#mainNav',
    offset: 75
  });

  // Collapse Navbar
  var navbarCollapse = function() {
    var $nav = $("#mainNav");
    if ($nav.length === 0) return;
    if ($nav.offset().top > 100) {
      $nav.addClass("navbar-scrolled");
    } else {
      $nav.removeClass("navbar-scrolled");
    }
  };
  // Collapse now if page is not at top
  navbarCollapse();
  // Collapse the navbar when page is scrolled
  $(window).scroll(navbarCollapse);

})(typeof jQuery !== 'undefined' ? jQuery : null);

/**
 * Alumni Portal - Global Utility Functions
 * These functions are used across forum.php, careers.php, gallery.php,
 * view_forum.php, view_event.php, alumni_list.php, and other pages.
 * They mirror the implementations in admin/index.php so that frontend
 * pages loaded outside the admin shell also work correctly.
 *
 * Each function is only defined if it does not already exist on `window`,
 * preventing conflicts when these pages are loaded inside admin/index.php
 * which defines its own copies.
 */
(function($) {
  if (!$) return; // Skip if jQuery not available

  // ── start_load / end_load ─────────────────────────────────────────────
  // Shows/hides a translucent loading overlay while AJAX requests run.

  if (typeof window.start_load !== 'function') {
    window.start_load = function() {
      if ($('#preloader2').length === 0) {
        $('body').prepend(
          '<div id="preloader2" style="position:fixed;z-index:99999;top:0;left:0;width:100vw;height:100vh;' +
          'background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;">' +
          '<div style="width:48px;height:48px;border-radius:50%;border:4px solid rgba(0,0,0,0.1);' +
          'border-top-color:#800000;animation:spin 1s linear infinite;"></div></div>'
        );
      }
    };
  }

  if (typeof window.end_load !== 'function') {
    window.end_load = function() {
      $('#preloader2').fadeOut('fast', function() {
        $(this).remove();
      });
    };
  }

  // ── uni_modal ─────────────────────────────────────────────────────────
  // Opens the shared Bootstrap modal, loads remote content via AJAX.
  //   title  - text for the modal header
  //   url    - URL whose HTML response is placed in the modal body
  //   size   - optional extra class for .modal-dialog (e.g. 'mid-large', 'large')

  if (typeof window.uni_modal !== 'function') {
    window.uni_modal = function($title, $url, $size) {
      $size = $size || '';
      start_load();
      $.ajax({
        url: $url,
        error: function(err) {
          console.error('uni_modal load error', err);
          alert('An error occurred');
          end_load();
        },
        success: function(resp) {
          if (resp) {
            $('#uni_modal .modal-title').html($title);
            $('#uni_modal .modal-body').html(resp);
            if ($size !== '') {
              $('#uni_modal .modal-dialog').addClass($size);
            } else {
              $('#uni_modal .modal-dialog').removeAttr('class').addClass('modal-dialog modal-md');
            }
            $('#uni_modal').modal({
              show: true,
              backdrop: 'static',
              keyboard: false,
              focus: true
            });
            end_load();
          }
        }
      });
    };
  }

  // ── viewer_modal ──────────────────────────────────────────────────────
  // Displays an image (or video) inside a full-screen-ish dark modal.

  if (typeof window.viewer_modal !== 'function') {
    window.viewer_modal = function($src) {
      $src = $src || '';
      start_load();
      var ext = $src.split('.').pop().toLowerCase();
      var view;
      if (ext === 'mp4') {
        view = $("<video src='" + $src + "' controls autoplay></video>");
      } else {
        view = $("<img src='" + $src + "' />");
      }
      $('#viewer_modal .modal-content video, #viewer_modal .modal-content img').remove();
      $('#viewer_modal .modal-content').append(view);
      $('#viewer_modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: false,
        focus: true
      });
      end_load();
    };
  }

  // ── _conf ─────────────────────────────────────────────────────────────
  // Shows a confirmation dialog. On "Continue", calls the named function
  // with the supplied params array.

  if (typeof window._conf !== 'function') {
    window._conf = function($msg, $func, $params) {
      $params = $params || [];
      $('#confirm_modal #confirm').attr('onclick', $func + '(' + $params.join(',') + ')');
      $('#confirm_modal .modal-body').html($msg);
      $('#confirm_modal').modal('show');
    };
  }

  // ── alert_toast ───────────────────────────────────────────────────────
  // Briefly shows a Bootstrap toast notification.
  //   msg  - the message text/html
  //   bg   - 'success' | 'danger' | 'info' | 'warning'

  if (typeof window.alert_toast !== 'function') {
    window.alert_toast = function($msg, $bg) {
      $msg = $msg || '';
      $bg  = $bg  || 'success';

      var $toast = $('#alert_toast');
      $toast.removeClass('bg-success bg-danger bg-info bg-warning');
      $toast.addClass('bg-' + $bg);
      $toast.find('.toast-body').html($msg);
      $toast.toast({ delay: 3000 }).toast('show');
    };
  }

})(typeof jQuery !== 'undefined' ? jQuery : null);
