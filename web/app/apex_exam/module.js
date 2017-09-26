define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_exam', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'apex_baseline',
        column: 'participant.uid'
      }
    },
    name: {
      singular: 'apex exam',
      plural: 'apex exams',
      possessive: 'apex exam\'s',
      pluralPossessive: 'apex exams\'',
      friendlyColumn: 'rank'
    },
    columnList: {
      uid: {
        column: 'participant.uid',
        title: 'Participant'
      },
      barcode: {
        title: 'Barcode'
      },
      site: {
        title: 'Site'
      },
      rank: {
        title: 'Wave Rank',
        type: 'rank'
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
      type: 'string',
      constant: true
    },
    height: {
      title: 'Height',
      type: 'string'
    },
    weight: {
      title: 'Weight',
      type: 'string'
    },
    age: {
      title: 'Age',
      type: 'string'
    },
    barcode: {
      title: 'Barcode',
      type: 'string'
    },
    site: {
      title: 'Site',
      type: 'string'
    },
    rank: {
      title: 'Wave Rank',
      type: 'rank'
    },
    technician: {
      title: 'Technician',
      type: 'string'
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexExamList', [
    'CnApexExamModelFactory',
    function( CnApexExamModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexExamModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexExamView', [
    'CnApexExamModelFactory',
    function( CnApexExamModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexExamModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexExamListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexExamViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexExamModelFactory', [
    'CnBaseModelFactory',
    'CnApexExamListFactory', 'CnApexExamViewFactory',
    function( CnBaseModelFactory,
              CnApexExamListFactory, CnApexExamViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnApexExamListFactory.instance( this );
        this.viewModel = CnApexExamViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
