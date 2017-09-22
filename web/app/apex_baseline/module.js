define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_baseline', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'participant',
        column: 'participant.uid'
      }
    },
    name: {
      singular: 'apex baseline',
      plural: 'apex baselines',
      possessive: 'apex baseline\'s',
      pluralPossessive: 'apex baselines\''
    },
    columnList: {
      uid: {
        column: 'participant.uid',
        title: 'Participant'
      },
      dob: {
        title: 'Date of Birth',
        type: 'dob'
      },
      ethnicity: {
        title: 'Ethnicity'
      },
      sex: {
        title: 'Sex'
      }
    },
    defaultOrder: {
      column: 'participant.uid',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    participant: {
      column: 'participant.uid',
      title: 'Participant',
      type: 'string'
    },
    dob: {
      title: 'Date of Birth',
      type: 'dob'
    },
    ethnicity: {
      title: 'Ethnicity',
      type: 'string'
    },
    sex: {
      title: 'Sex',
      type: 'string'
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexBaselineList', [
    'CnApexBaselineModelFactory',
    function( CnApexBaselineModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexBaselineModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexBaselineView', [
    'CnApexBaselineModelFactory',
    function( CnApexBaselineModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexBaselineModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexBaselineListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexBaselineViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexBaselineModelFactory', [
    'CnBaseModelFactory',
    'CnApexBaselineListFactory', 'CnApexBaselineViewFactory',
    function( CnBaseModelFactory,
              CnApexBaselineListFactory, CnApexBaselineViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnApexBaselineListFactory.instance( this );
        this.viewModel = CnApexBaselineViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
