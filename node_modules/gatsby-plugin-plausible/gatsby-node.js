"use strict";

exports.onPreInit = function (_ref, options) {
  var reporter = _ref.reporter;

  if (!options.domain) {
    reporter.warn("The Plausible Analytics plugin requires a domain. Did you mean to add it?");
  }
};