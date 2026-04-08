import app from 'flarum/admin/app';

app.initializers.add('glowingblue-date-filters', () => {
	app.extensionData.for('glowingblue-date-filters').registerPermission(
		{
			icon: 'fas fa-calendar',

			label: app.translator.trans('glowingblue-date-filters.admin.permissions.view_date_filter_label'),
			permission: 'viewDateFilter',
			allowGuest: true,
		},
		'moderate'
	);
});
