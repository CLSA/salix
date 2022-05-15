"use strict";

var cenozo = angular.module("cenozo");

cenozo.controller("HeaderCtrl", [
  "$scope",
  "CnBaseHeader",
  async function ($scope, CnBaseHeader) {
    // copy all properties from the base header
    await CnBaseHeader.construct($scope);
  },
]);
