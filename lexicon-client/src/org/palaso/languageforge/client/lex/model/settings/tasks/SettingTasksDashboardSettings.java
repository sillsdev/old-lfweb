package org.palaso.languageforge.client.lex.model.settings.tasks;

import org.palaso.languageforge.client.lex.model.BaseDto;

public class SettingTasksDashboardSettings extends BaseDto<SettingTasksDashboardSettings> {

	protected SettingTasksDashboardSettings() {
	};

	public final static SettingTasksDashboardSettings getNew() {
		SettingTasksDashboardSettings entry = SettingTasksDashboardSettings
				.createObject().cast();
		entry.setActivityTimeRange(30);
		entry.setTargetWordCount(0);
		return entry;
	}

	public final native int getActivityTimeRange() /*-{
		if (this.hasOwnProperty('activitytimerange')) {
			if (this.activitytimerange.hasOwnProperty('$')) {
				return this.activitytimerange['$'];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}-*/;

	public final native void setActivityTimeRange(int range) /*-{
		if (!this.hasOwnProperty('activitytimerange')) {
			this.activitytimerange = new Object();
		}
		if (!this.activitytimerange.hasOwnProperty('$')) {
			this.activitytimerange['$'] = new Object();
		}
		this.activitytimerange['$'] = range;
	}-*/;

	public final native int getTargetWordCount() /*-{
		if (this.hasOwnProperty('targetwordcount')) {
			if (this.targetwordcount.hasOwnProperty('$')) {
				return this.targetwordcount['$'];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}-*/;

	public final native void setTargetWordCount(int count) /*-{
		if (!this.hasOwnProperty('targetwordcount')) {
			this.targetwordcount = new Object();
		}
		if (!this.targetwordcount.hasOwnProperty('$')) {
			this.targetwordcount['$'] = new Object();
		}
		this.targetwordcount['$'] = count;
	}-*/;

}
