define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_deployment', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'apex_scan',
        column: 'apex_scan.id'
      }
    },
    name: {
      singular: 'apex deployment',
      plural: 'apex deployments',
      possessive: 'apex deployment\'s',
      pluralPossessive: 'apex deployments\''
    },
    columnList: {
      uid: {
        column: 'participant.uid',
        title: 'Participant'
      },
      rank: {
        column: 'apex_exam.rank',
        title: 'Wave Rank',
        type: 'rank'
      },
      scan_type_type: {
        column: 'scan_type.type',
        title: 'Scan Type'
      },
      scan_type_side: {
        column: 'scan_type.side',
        title: 'Scan Side'
      },
      apex_host: {
        column: 'apex_host.name',
        title: 'Apex Host'
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
      column: 'apex_host.id',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    apex_host_id: {
      title: 'Apex Host',
      type: 'enum'
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
      type: 'string',
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
      type: 'string',
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
      title: 'Note',
      type: 'text'
    },
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexDeploymentAdd', [
    'CnApexDeploymentModelFactory',
    function( CnApexDeploymentModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'add.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexDeploymentModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexDeploymentList', [
    'CnApexDeploymentModelFactory',
    function( CnApexDeploymentModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexDeploymentModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexDeploymentView', [
    'CnApexDeploymentModelFactory',
    function( CnApexDeploymentModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexDeploymentModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentAddFactory', [
    'CnBaseAddFactory',
    function( CnBaseAddFactory ) {
      var object = function( parentModel ) { CnBaseAddFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexDeploymentModelFactory', [
    'CnBaseModelFactory',
    'CnApexDeploymentAddFactory', 'CnApexDeploymentListFactory', 'CnApexDeploymentViewFactory',
    'CnHttpFactory',
    function( CnBaseModelFactory,
              CnApexDeploymentAddFactory, CnApexDeploymentListFactory, CnApexDeploymentViewFactory,
              CnHttpFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.addModel = CnApexDeploymentAddFactory.instance( this );
        this.listModel = CnApexDeploymentListFactory.instance( this );
        this.viewModel = CnApexDeploymentViewFactory.instance( this, root );

        // extend getMetadata
        this.getMetadata = function() {
          return this.$$getMetadata().then( function() {
            return CnHttpFactory.instance( {
              path: 'apex_host',
              data: {
                select: { column: [ 'id', 'name' ] },
                modifier: { order: { name: false } }
              }
            } ).query().then( function success( response ) { 
              self.metadata.columnList.apex_host_id.enumList = []; 
              response.data.forEach( function( item ) { 
                self.metadata.columnList.apex_host_id.enumList.push( {
                  value: item.id,
                  name: item.name
                } );
              } );
            } );
          } );
        };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );