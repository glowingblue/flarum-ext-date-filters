import app from 'flarum/forum/app';
import setRouteWithForcedRefresh from 'flarum/common/utils/setRouteWithForcedRefresh';
import GlobalSearchState from 'flarum/forum/states/GlobalSearchState';

export default function extendGlobalSearchState() {
	GlobalSearchState.prototype.changeDate = function (date: string): void {
		const params = this.params();

		if (date === Object.keys(app.discussions.dateFilterMap())[0]) {
			delete params.date;
		} else {
			params.date = date;
		}

		setRouteWithForcedRefresh(app.route(app.current.get('routeName'), params));
	};
}
