define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_deployment', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'apex deployment',
      plural: 'apex deployments',
      possessive: 'apex deployment\'s',
      pluralPossessive: 'apex deployments\''
    },
    columnList: {
      apex_host: {
        column: 'apex_host.id',
        title: 'Serial Number'
      },
      site: {
        column: 'site.name',
        title: 'Site'
      },
      merged: {
        title: 'Merged',
        type: 'boolean'
      },
      priority: {
        title: 'Priority',
        type: 'boolean'
      },
      status: {
        title: 'Status'
      },
      pass: {
        title: 'Pass',
        type: 'boolean'
      }
    },
    defaultOrder: {
      column: 'site.name',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    apex_host_id: {
      title: 'Serial Number',
      type: 'enum'
    },
    site: {
      column: 'site.name',
      title: 'Site',
      exclude: 'add',
      constant: true
    },
    merged: {
      title: 'Merged',
      type: 'boolean',
      exclude: 'add',
      constant: true
    },
    priority: {
      title: 'Priority',
      type: 'boolean'
    },
    status: {
      title: 'Status',
      exclude: 'add',
      constant: true
    },
    pass: {
      title: 'Pass',
      type: 'boolean',
      exclude: 'add',
      constant: true
    },
    comp_scanid: {
      title: 'Scan ID',
      exclude: 'add',
      constant: true
    },
    analysis_datetime: {
      title: 'Analysis Date & Time',
      type: 'datetime',
      exclude: 'add',
      constant: true
    },
    export_datetime: {
      title: 'Export Date & Time',
      type: 'datetime',
      exclude: 'add',
      constant: true
    },
    import_datetime: {
      title: 'Import Date & Time',
      type: 'datetime',
      exclude: 'add',
      constant: true
    },
    note: {
      title: '',
      type: 'text'
    },
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexHostList', [
    'CnApexHostModelFactory',
    function( CnApexHostModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexHostModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexHostView', [
    'CnApexHostModelFactory',
    function( CnApexHostModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexHostModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexHostListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexHostViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexHostModelFactory', [
    'CnBaseModelFactory',
    'CnApexHostListFactory', 'CnApexHostViewFactory',
    function( CnBaseModelFactory,
              CnApexHostListFactory, CnApexHostViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnApexHostListFactory.instance( this );
        this.viewModel = CnApexHostViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
