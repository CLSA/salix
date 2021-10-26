cenozoApp.defineModule( { name: 'scan_type', models: ['add', 'list', 'view'], defaultTab: 'apex_scan', create: module => {

  angular.extend( module, {
    identifier: { column: [ 'type', 'side' ] },
    name: {
      singular: 'scan type',
      plural: 'scan types',
      possessive: 'scan type\'s'
    },
    columnList: {
      type: {
        title: 'Type'
      },
      side: {
        title: 'Side'
      },
      code_type_count: {
        title: 'Code Types',
        type: 'number'
      }
    },
    defaultOrder: {
      column: 'scan_type.type',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    type: {
      title: 'Type',
      type: 'string'
    },
    side: {
      title: 'Side',
      type: 'string'
    }
  } );

} } );
