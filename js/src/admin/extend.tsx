import Extend from 'flarum/common/extenders';
import app from 'flarum/admin/app';

export default [
  new Extend.Admin().permission(
    () => ({
      icon: 'fas fa-calendar',
      label: app.translator.trans('glowingblue-date-filters.admin.permissions.view_date_filter_label'),
      permission: 'viewDateFilter',
      allowGuest: true,
    }),
    'moderate'
  ),
] satisfies object[];
