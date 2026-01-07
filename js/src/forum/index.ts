import app from 'flarum/forum/app';
import extendDiscussionListState from './extenders/extendDiscussionListState';
import extendGlobalSearchState from './extenders/extendGlobalSearchState';

app.initializers.add('glowingblue-date-filters', () => {
	// nothing here yet
	extendGlobalSearchState();
	extendDiscussionListState();
});
