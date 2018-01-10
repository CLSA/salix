define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'serial_number', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: { column: 'serial_number' },
    name: {
      singular: 'serial number',
      plural: 'serial numbers',
      possessive: 'serial number\'s',
      pluralPossessive: 'serial numbers\''
    },
    columnList: {
      site: {
        column: 'site.name',
        title: 'Site'
      },
      serial_number: {
        title: 'Number'
      }
    },
    defaultOrder: {
      column: 'site.name',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    site: {
      title: 'Site',
      column: 'site.name',
      type: 'string',
      constant: true
    },
    serial_number: {
      title: 'Number',
      type: 'string'
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnSerialNumberAdd', [
    'CnSerialNumberModelFactory',
    function( CnSerialNumberModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'add.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnSerialNumberModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnSerialNumberList', [
    'CnSerialNumberModelFactory',
    function( CnSerialNumberModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnSerialNumberModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnSerialNumberView', [
    'CnSerialNumberModelFactory',
    function( CnSerialNumberModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnSerialNumberModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSerialNumberAddFactory', [
    'CnBaseAddFactory',
    function( CnBaseAddFactory ) {
      var object = function( parentModel ) { CnBaseAddFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSerialNumberListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSerialNumberViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSerialNumberModelFactory', [
    'CnBaseModelFactory',
    'CnSerialNumberAddFactory', 'CnSerialNumberListFactory', 'CnSerialNumberViewFactory',
    function( CnBaseModelFactory,
              CnSerialNumberAddFactory, CnSerialNumberListFactory, CnSerialNumberViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.addModel = CnSerialNumberAddFactory.instance( this );
        this.listModel = CnSerialNumberListFactory.instance( this );
        this.viewModel = CnSerialNumberViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
