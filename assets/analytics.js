// Visit counting is done by NordAnalytics (first-party, cookieless, see /privacy.html#website-statistics).
// Google Analytics and Google Fonts were removed in the same release that added the tag.

// Footer notice + opt-out links.
(function () {
  function addOptOutLink() {
    var fb = document.querySelector('.foot-bottom, .footer-bottom, .foot-base, .footer-base') || document.querySelector('footer .container') || document.querySelector('footer');
    if (!fb || fb.querySelector('.nord-optout')) return;
    var wrap = document.createElement('span');
    wrap.className = 'nord-stats-links';
    wrap.style.display = 'inline-block';
    var info = document.createElement('a');
    info.href = '/privacy.html#website-statistics';
    info.textContent = 'How we count visits';
    var a = document.createElement('a');
    a.href = '#';
    a.className = 'nord-optout';
    a.textContent = "Don't count my visits";
    var msg = document.createElement('span');
    msg.className = 'nord-optout-msg';
    msg.setAttribute('data-na-optout-done', '');
    msg.hidden = true;
    msg.setAttribute('role', 'status');
    msg.setAttribute('aria-live', 'polite');
    a.addEventListener('click', function (e) {
      e.preventDefault();
      if (typeof window.nordAnalyticsOptOut === 'function') {
        try { window.nordAnalyticsOptOut(); } catch (err) {}
        msg.textContent = " Done, we won't count your visits.";
      } else {
        try { localStorage.setItem('na_ignore', '1'); } catch (err) {}
        msg.textContent = " Our visit counter isn't running yet, so there is nothing to switch off.";
      }
      msg.hidden = false;
    });
    wrap.appendChild(info);
    wrap.appendChild(document.createTextNode(' \u00b7 '));
    wrap.appendChild(a);
    wrap.appendChild(msg);
    fb.appendChild(wrap);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', addOptOutLink);
  } else {
    addOptOutLink();
  }
})();

// NordAnalytics tag: cookieless, skipped when the visitor opted out.
(function () {
  try { if (localStorage.getItem('na_ignore') === '1') return; } catch (e) {}
  var s = document.createElement('script');
  s.async = true;
  s.src = 'https://inbox.nordsys.co.uk/analytics/a.js';
  document.head.appendChild(s);
})();
