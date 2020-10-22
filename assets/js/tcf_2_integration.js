;
(function($) {
  var cnt = 0,
    consentSetInterval = setInterval(function() {
      if (++cnt === 600) {
        clearInterval(consentSetInterval);
      }

      if (typeof window.__tcfapi === 'undefined') {
        return;
      }
      clearInterval(consentSetInterval);

      window.__tcfapi('addEventListener', 2, function(TCData, listenerSuccess) {
          if (!listenerSuccess) {
            return;
          }

          if (TCData.eventStatus === 'tcloaded' || TCData.eventStatus === 'useractioncomplete') {
            var userAction = TCData.eventStatus === 'useractioncomplete';
            if (!TCData.gdprApplies) {
              if (quads_tcf_2.state !== 'not_needed') {
                quads_dispatch_event('not_needed');
              }
              return;
            }

            if (TCData.purpose.consents[1]) {
              if (quads_tcf_2.state !== 'accepted') {
                quads_dispatch_event('accepted');
              }
              return;
            }

            // fire another event, in case the user revokes the previous consent.
            if (quads_tcf_2.state !== 'rejected') {
              quads_dispatch_event('rejected');
            }
          }
        }

      );

    })
})(window.jQuery);

function quads_get_data(state) {
  if (
    (state !== 'accepted' && state !== 'not_needed') ||
    document.readyState === 'loading'
  ) {
    return;
  }
  document.querySelectorAll('script[type="text/plain"][data-tcf="waiting-for-consent"]').forEach(function(el, inject) {
    // this can also be a number if used in a foreach.
    inject = typeof inject === 'boolean' ? inject : true;
    var string = decodeURIComponent(Array.prototype.map.call(atob(el.textContent), function(c) {
      return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    }).join(''));

    if (!inject) {
      return string;
    }

    el.replaceWith(document.createRange().createContextualFragment(string));
  });

}

function quads_dispatch_event(state) {
  var previousState = state,
    fire_event = function() {

      quads_get_data(state);

    };
  // DOM is ready.
  if (document.readyState !== 'loading') {
    return fire_event();
  }
  // If DOM is still loading, wait for it to be interactive/complete.
  document.addEventListener('readystatechange', fire_event, {
    once: true
  });
}