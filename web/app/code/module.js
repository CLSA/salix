define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'code', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'code',
      plural: 'codes',
      possessive: 'code\'s'
    },
    columnList: {
      code_type: {
        column: 'code_type.code',
        title: 'Code Type',
      },
      user: {
        column: 'user.name',
        title: 'User'
      }
    },
    defaultOrder: {
      column: 'code_type.code',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    code_type_id: {
      title: 'Code Type',
      type: 'enum'
    },
    user_id: {
      title: 'User',
      type: 'lookup-typeahead',
      typeahead: {
        table: 'user',
        select: 'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )',
        where: [ 'user.first_name', 'user.last_name', 'user.name' ]
      }
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnCodeAdd', [
    'CnCodeModelFactory',
    function( CnCodeModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'add.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnCodeModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnCodeList', [
    'CnCodeModelFactory',
    function( CnCodeModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnCodeModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnCodeView', [
    'CnCodeModelFactory',
    function( CnCodeModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnCodeModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnCodeAddFactory', [
    'CnBaseAddFactory',
    function( CnBaseAddFactory ) {
      var object = function( parentModel ) { CnBaseAddFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnCodeListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnCodeViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnCodeModelFactory', [
    'CnBaseModelFactory', 'CnCodeAddFactory', 'CnCodeListFactory', 'CnCodeViewFactory', 'CnHttpFactory',
    function( CnBaseModelFactory, CnCodeAddFactory, CnCodeListFactory, CnCodeViewFactory, CnHttpFactory ) {
      var object = function( root ) {
        CnBaseModelFactory.construct( this, module );
        this.addModel = CnCodeAddFactory.instance( this );
        this.listModel = CnCodeListFactory.instance( this );
        this.viewModel = CnCodeViewFactory.instance( this, root );

        // extend getMetadata
        this.getMetadata = async function() {
          var self = this;
          await this.$$getMetadata();

          var response = await CnHttpFactory.instance( {
            path: 'code_type',
            data: {
              select: { column: [ 'id', 'code' ] },
              modifier: { order: 'code', limit: 1000 }
            }
          } ).query();

          this.metadata.columnList.code_type_id.enumList = []; 
          response.data.forEach( function( item ) { 
            self.metadata.columnList.code_type_id.enumList.push( {
              value: item.id,
              name: item.code
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
