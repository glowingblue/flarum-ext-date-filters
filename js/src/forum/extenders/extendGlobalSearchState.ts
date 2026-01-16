import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import GlobalSearchState from 'flarum/forum/states/GlobalSearchState';
import DateFilter from '../../common/DateFilter';

export default function extendGlobalSearchState() {
	extend(GlobalSearchState.prototype, 'stickyParams', function (params: Record<string, string>): void {
		const date = m.route.param('date');
		if (date) params.date = date;

		const start = m.route.param('start');
		const end = m.route.param('end');
		if (date === DateFilter.CUSTOM_DATE_PRESET) {
			if (start) params.start = start;
			if (end) params.end = end;
		}
	});

	GlobalSearchState.prototype.changeDate = function (date: string, range?: { start?: string; end?: string }): void {
		const params = this.params();

		if (date === DateFilter.DEFAULT_DATE_PRESET) {
			delete params.date;
			delete params.start;
			delete params.end;
		} else {
			params.date = date;

			if (date === DateFilter.CUSTOM_DATE_PRESET) {
				if (range?.start) params.start = range.start;
				else delete params.start;

				if (range?.end) params.end = range.end;
				else delete params.end;
			} else {
				delete params.start;
				delete params.end;
			}
		}

		setRouteWithForcedRefresh(app.route(app.current.get('routeName'), params));
	};
}
