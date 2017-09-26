define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'apex_scan', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'apex_exam',
        column: 'apex_exam.id'
      }
    },
    name: {
      singular: 'apex scan',
      plural: 'apex scans',
      possessive: 'apex scan\'s',
      pluralPossessive: 'apex scans\''
    },
    columnList: {
      uid: {
        column: 'participant.uid',
        title: 'Participant'
      },
      host: {
        column: 'apex_host.name',
        title: 'Workstation'
      },
      type: {
        title: 'Type'
      },
      side: {
        title: 'Side'
      },
      status: {
        title: 'Status'
      },
      pass: {
        title: 'Pass',
        type: 'boolean'
      },
      priority: {
        title: 'Priority'
      },
      analysis_datetime: {
        title: 'Analysis Date & Time',
        type: 'datetime'
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
    rank: {
      column: 'apex_exam.rank',
      title: 'Wave Rank',
      type: 'string',
      constant: true
    },
    host: {
      column: 'apex_host.name',
      title: 'Apex Workstation',
      type: 'string',
      constant: true
    },
    type: {
      title: 'Type',
      type: 'string',
      constant: true
    },
    side: {
      title: 'Side',
      type: 'string',
      constant: true
    },
    availability: {
      title: 'Availability',
      type: 'string',
      constant: true,
      help: '0: no files are available, 1: left scan only, 2: right scan only, 3: left and right scans available'
    },
    status: {
      title: 'Status',
      type: 'string',
      constant: true
    },
    pass: {
      title: 'QC Pass',
      type: 'boolean',
      constant: true
    },
    merged: {
      title: 'Merged',
      type: 'boolean',
      constant: true
    },
    priority: {
      title: 'Priority',
      type: 'boolean'
    },
    analysis_datetime: {
      title: 'Analysis Date & Time',
      type: 'datetime',
      constant: true
    },
    scan_datetime: {
      title: 'Scan Date & Time',
      type: 'datetime',
      constant: true
    },
    serial_number: {
      title: 'Serial Number',
      type: 'string',
      constant: true
    },
    code_list: {
      title: 'Code List',
      type: 'string'
    },
    note: {
      title: 'Note',
      type: 'text'
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexScanList', [
    'CnApexScanModelFactory',
    function( CnApexScanModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexScanModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApexScanView', [
    'CnApexScanModelFactory',
    function( CnApexScanModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnApexScanModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexScanListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexScanViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApexScanModelFactory', [
    'CnBaseModelFactory',
    'CnApexScanListFactory', 'CnApexScanViewFactory',
    function( CnBaseModelFactory,
              CnApexScanListFactory, CnApexScanViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnApexScanListFactory.instance( this );
        this.viewModel = CnApexScanViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
