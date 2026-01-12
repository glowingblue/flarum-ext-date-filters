import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import GlobalSearchState from 'flarum/forum/states/GlobalSearchState';
import DateFilter from '../../common/DateFilter';

export default function extendGlobalSearchState() {
	extend(GlobalSearchState.prototype, 'stickyParams', function (params: Record<string, string>): void {
		const date = m.route.param('date');

		if (date) params.date = date;
	});

	GlobalSearchState.prototype.changeDate = function (date: string): void {
		const params = this.params();

		if (date === DateFilter.DEFAULT_DATE_PRESET) {
			delete params.date;
		} else {
			params.date = date;
		}

		setRouteWithForcedRefresh(app.route(app.current.get('routeName'), params));
	};
}
