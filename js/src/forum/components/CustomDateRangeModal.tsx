import app from 'flarum/common/app';
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import ItemList from 'flarum/common/utils/ItemList';
import { FORMAT } from '../utils/createdRangeForPreset';

import type { NestedStringArray } from '@askvortsov/rich-icu-message-formatter';
import type Mithril from 'mithril';

export interface ICustomDateRangeModalAttrs extends IInternalModalAttrs {
	// nothing here yet
}

export default class CustomDateRangeModal<CustomAttrs extends ICustomDateRangeModalAttrs = ICustomDateRangeModalAttrs> extends Modal<CustomAttrs> {
	className(): string {
		return 'CustomDateRangeModal Modal--small';
	}

	title(): string | NestedStringArray {
		return app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.title');
	}

	content(): [Mithril.Children, Mithril.Children] {
		return [<div className="Modal-body">{this.body()}</div>, <div className="Modal-footer">{this.footer()}</div>];
	}

	body(): JSX.Element {
		const today = dayjs().startOf('day');

		return (
			<div>
				<label for="start">{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.start_date_label')}</label>
				<br></br>

				<input type="date" id="start" name="range-start" max={today.subtract(1, 'day').format(FORMAT)} />

				<br />
				<br />

				<label for="end">{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.end_date_label')}</label>
				<br></br>

				<input type="date" id="end" name="range-end" value={today.format(FORMAT)} max={today.format(FORMAT)} />
			</div>
		);
	}

	footer(): JSX.Element | null {
		return <>{this.footerItems().toArray()}</>;
	}

	footerItems(): ItemList<Mithril.Children> {
		const items = new ItemList<Mithril.Children>();

		items.add(
			'cancel',
			<Button className="Button" onclick={() => this.hide()}>
				{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.cancel_button')}
			</Button>
		);

		items.add(
			'apply',
			<Button
				//type="submit"
				className="Button Button--primary"
				onclick={() => console.log('Apply custom date range')}
			>
				{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.apply_button')}
			</Button>
		);

		return items;
	}
}
