import app from 'flarum/forum/app';
import { IFormModalAttrs } from 'flarum/common/components/FormModal';
import FormModal from 'flarum/common/components/FormModal';
import Button from 'flarum/common/components/Button';
import ItemList from 'flarum/common/utils/ItemList';
import { isValidDay, utcToday } from '../utils/createDateRangeUtil';

import type Mithril from 'mithril';

export interface ICustomDateRangeModalAttrs extends IFormModalAttrs {
	onApply?: (range: { start: string; end: string }) => void;
}

export default class CustomDateRangeModal<
	CustomAttrs extends ICustomDateRangeModalAttrs = ICustomDateRangeModalAttrs,
> extends FormModal<CustomAttrs> {
	protected start = utcToday();
	protected end = utcToday();

	private pendingApply: { start: string; end: string } | null = null;

	oninit(vnode: Mithril.Vnode) {
		super.oninit(vnode);

		const params = app.search?.state?.params?.() ?? {};
		if (params.start) this.start = params.start;
		if (params.end) this.end = params.end;
	}

	onremove(vnode: Mithril.Vnode) {
		super.onremove(vnode);

		if (this.pendingApply) {
			this.attrs.onApply?.(this.pendingApply);
			this.pendingApply = null;
		}
	}

	className(): string {
		return 'CustomDateRangeModal Modal--small';
	}

	title(): Mithril.Children {
		return app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.title');
	}

	content(): [Mithril.Children, Mithril.Children] {
		return [<div className="Modal-body">{this.body()}</div>, <div className="Modal-footer">{this.footer()}</div>];
	}

	body(): JSX.Element {
		const maxEnd = utcToday();

		return (
			<div>
				<label htmlFor="start">{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.start_date_label')}</label>
				<br />
				<input
					type="date"
					id="start"
					name="range-start"
					value={this.start}
					max={maxEnd}
					oninput={(e: InputEvent) => {
						this.start = (e.target as HTMLInputElement).value;

						if (isValidDay(this.start) && isValidDay(this.end) && this.end < this.start) {
							this.end = this.start;
						}
					}}
				/>

				<br />
				<br />

				<label htmlFor="end">{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.end_date_label')}</label>
				<br />
				<input
					type="date"
					id="end"
					name="range-end"
					value={this.end}
					max={maxEnd}
					oninput={(e: InputEvent) => {
						this.end = (e.target as HTMLInputElement).value;

						if (isValidDay(this.start) && isValidDay(this.end) && this.start > this.end) {
							this.start = this.end;
						}
					}}
				/>
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
				className="Button Button--primary"
				disabled={!this.isValid()}
				onclick={() => {
					if (!this.isValid()) return;

					this.pendingApply = { start: this.start, end: this.end };
					this.hide();
				}}
			>
				{app.translator.trans('glowingblue-date-filters.forum.custom_date_range_modal.apply_button')}
			</Button>
		);

		return items;
	}

	private isValid(): boolean {
		// YYYY-MM-DD strings sort in date order.
		return isValidDay(this.start) && isValidDay(this.end) && this.end >= this.start;
	}
}
