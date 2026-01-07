import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import Dropdown from 'flarum/common/components/Dropdown';
import Button from 'flarum/common/components/Button';

import type Mithril from 'mithril';
import type ItemList from 'flarum/common/utils/ItemList';

export default function addFilterOptionsToIndexPageToolbar() {
	extend(IndexPage.prototype, 'viewItems', function (items: ItemList<Mithril.Children>) {
		const dateFilterMap = app.discussions.dateFilterMap();

		const dateFilterOptions = Object.keys(dateFilterMap).reduce((acc: any, dateFilterId) => {
			acc[dateFilterId] = app.translator.trans(`glowingblue-date-filters.forum.index_date_filter.${dateFilterId}_button`);
			return acc;
		}, {});

		items.add(
			'date',
			<Dropdown
				buttonClassName="Button"
				icon="far fa-calendar-alt"
				label={dateFilterOptions[app.search.params().date] || Object.keys(dateFilterMap).map((key) => dateFilterOptions[key])[0]}
				accessibleToggleLabel={app.translator.trans('glowingblue-date-filters.forum.index_date_filter.toggle_dropdown_accessible_label')}
			>
				{Object.keys(dateFilterOptions).map((value) => {
					const label = dateFilterOptions[value];
					const active = (app.search.params().date || Object.keys(dateFilterMap)[0]) === value;

					return (
						<Button icon={active ? 'fas fa-check' : true} onclick={app.search.changeDate.bind(app.search, value)} active={active}>
							{label}
						</Button>
					);
				})}
			</Dropdown>
		);
	});
}
