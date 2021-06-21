"use strict";

exports.onRouteUpdate = function (_ref) {
  var location = _ref.location;

  if (process.env.NODE_ENV === "production" && typeof window.plausible === "object") {
    var pathIsExcluded = location && typeof window.plausibleExcludePaths !== "undefined" && window.plausibleExcludePaths.some(function (rx) {
      return rx.test(location.pathname);
    });
    if (pathIsExcluded) return null;
    window.plausible('pageview');
  }
};