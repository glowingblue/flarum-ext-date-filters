import app from 'flarum/forum/app';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import GlobalSearchState from 'flarum/forum/states/GlobalSearchState';
import DateFilter from '../../common/DateFilter';

export default function extendGlobalSearchState() {
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
