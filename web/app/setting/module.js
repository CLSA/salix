define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'setting', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'site',
        column: 'site_id',
        friendly: 'site'
      }
    },
    name: {
      singular: 'setting',
      plural: 'settings',
      possessive: 'setting\'s'
    },
    columnList: {
      site: {
        column: 'site.name',
        title: 'Site'
      },
      priority_apex_host: {
        column: 'apex_host.name',
        title: 'Priority Apex Host'
      }
    },
    defaultOrder: {
      column: 'site',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    site: {
      column: 'site.name',
      title: 'Site',
      type: 'string',
      isConstant: true
    },
    priority_apex_host_id: {
      title: 'Priority Apex Host',
      type: 'enum',
      help: 'Determines which Apex Host priority scans are deployed to'
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnSettingList', [
    'CnSettingModelFactory',
    function( CnSettingModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnSettingModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnSettingView', [
    'CnSettingModelFactory',
    function( CnSettingModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnSettingModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSettingListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSettingViewFactory', [
    'CnBaseViewFactory', 'CnSession',
    function( CnBaseViewFactory, CnSession ) {
      var object = function( parentModel, root ) {
        CnBaseViewFactory.construct( this, parentModel, root );

        // update the session data after patching settings
        this.afterPatch( function() { CnSession.updateData(); } );
      }
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnSettingModelFactory', [
    'CnBaseModelFactory', 'CnSettingListFactory', 'CnSettingViewFactory', 'CnHttpFactory',
    function( CnBaseModelFactory, CnSettingListFactory, CnSettingViewFactory, CnHttpFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnSettingListFactory.instance( this );
        this.viewModel = CnSettingViewFactory.instance( this, root );

        // extend getMetadata
        this.getMetadata = function() {
          return this.$$getMetadata().then( function() {
           return CnHttpFactory.instance( {
              path: 'apex_host',
              data: {
                select: { column: [ 'id', 'name' ] },
                modifier: { order: 'name', limit: 1000 }
              }
            } ).query().then( function success( response ) {
              self.metadata.columnList.priority_apex_host_id.enumList = [];
              response.data.forEach( function( item ) {
                self.metadata.columnList.priority_apex_host_id.enumList.push( {
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
