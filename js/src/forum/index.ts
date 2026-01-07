import app from 'flarum/forum/app';
import extendDiscussionListState from './extenders/extendDiscussionListState';

app.initializers.add('glowingblue-date-filters', () => {
	// nothing here yet
	extendDiscussionListState();
});
