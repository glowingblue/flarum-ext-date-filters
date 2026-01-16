import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import DateFilterDropdown from '../components/DateFilterDropdown';
import type { NestedStringArray } from '@askvortsov/rich-icu-message-formatter';

import type Mithril from 'mithril';
import type ItemList from 'flarum/common/utils/ItemList';

export default function addFilterOptionsToIndexPageToolbar() {
	extend(IndexPage.prototype, 'viewItems', function (items: ItemList<Mithril.Children>) {
		const dateFilterMap = app.discussions.dateFilterMap();

		const dateFilterOptions = Object.keys(dateFilterMap).reduce((dateFilterOptionsMap: Record<string, NestedStringArray>, dateFilterId) => {
			dateFilterOptionsMap[dateFilterId] = app.translator.trans(`glowingblue-date-filters.forum.index_date_filter.${dateFilterId}_button`);
			return dateFilterOptionsMap;
		}, {});

		items.add('date', <DateFilterDropdown dateFilterMap={dateFilterMap} dateFilterOptions={dateFilterOptions} />);
	});
}
