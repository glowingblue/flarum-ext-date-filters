import app from 'flarum/forum/app';
import Dropdown from 'flarum/common/components/Dropdown';
import Button from 'flarum/common/components/Button';
import Component, { ComponentAttrs } from 'flarum/common/Component';
import Separator from 'flarum/common/components/Separator';
import DateFilter from '../../common/DateFilter';
import CustomDateRangeModal from '../components/CustomDateRangeModal';
import ItemList from 'flarum/common/utils/ItemList';

import type Mithril from 'mithril';
import type { NestedStringArray } from '@askvortsov/rich-icu-message-formatter';

export interface IDateFilterDropdownAttrs extends ComponentAttrs {
  dateFilterMap: Record<string, string>;
  dateFilterOptions: Record<string, NestedStringArray>;
}

export default class DateFilterDropdown<CustomAttrs extends IDateFilterDropdownAttrs = IDateFilterDropdownAttrs> extends Component<CustomAttrs> {
  view(): JSX.Element {
    return (
      <Dropdown
        buttonClassName="Button"
        icon="far fa-calendar-alt"
        label={this.dateFilterDropdownLabel()}
        accessibleToggleLabel={app.translator.trans('glowingblue-date-filters.forum.index_date_filter.toggle_dropdown_accessible_label')}
      >
        {this.dateFilterDropdownItems().toArray()}
      </Dropdown>
    );
  }

  dateFilterDropdownLabel(): string | NestedStringArray {
    return (
      (app.search.params().date === DateFilter.CUSTOM_DATE_PRESET &&
        (app.search.params().start && app.search.params().end
          ? `${app.search.params().start} – ${app.search.params().end}`
          : app.translator.trans('glowingblue-date-filters.forum.index_date_filter.custom_date_range_button'))) ||
      this.attrs.dateFilterOptions[app.search.params().date] ||
      Object.keys(this.attrs.dateFilterMap).map((key) => this.attrs.dateFilterOptions[key])[0]
    );
  }

  dateFilterDropdownItems(): ItemList<Mithril.Children> {
    const items = new ItemList<Mithril.Children>();

    items.add('preset-filters', this.presetFilterOptionsView(), 100);
    items.add('separator', <Separator />, 80);
    items.add('custom-range', this.customRangeView(), 60);

    return items;
  }

  presetFilterOptionsView() {
    return Object.keys(this.attrs.dateFilterOptions).map((value) => {
      const label = this.attrs.dateFilterOptions[value];
      const active = (app.search.params().date || Object.keys(this.attrs.dateFilterMap)[0]) === value;

      return (
        <Button icon={active ? 'fas fa-check' : true} onclick={app.search.changeDate.bind(app.search, value)} active={active}>
          {label}
        </Button>
      );
    });
  }

  customRangeView() {
    return (
      <Button
        icon={(app.search.params().date || Object.keys(this.attrs.dateFilterMap)[0]) === DateFilter.CUSTOM_DATE_PRESET ? 'fas fa-check' : true}
        onclick={() =>
          app.modal.show(CustomDateRangeModal, {
            onApply: (range: { start: string; end: string }) => {
              app.search.changeDate('custom', range);
            },
          })
        }
        active={(app.search.params().date || Object.keys(this.attrs.dateFilterMap)[0]) === DateFilter.CUSTOM_DATE_PRESET}
      >
        {app.translator.trans('glowingblue-date-filters.forum.index_date_filter.custom_date_range_button')}
      </Button>
    );
  }
}
