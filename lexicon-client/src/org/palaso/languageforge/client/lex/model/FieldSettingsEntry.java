package org.palaso.languageforge.client.lex.model;

import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.core.client.JsArrayString;

public class FieldSettingsEntry extends JavaScriptObject {

	protected FieldSettingsEntry() {
	}

	public final native String getLabel() /*-{
		return this.Label;
	}-*/;

	public final native void setLabel(String label) /*-{
		this.Label=label;
	}-*/;
	
	public final native JsArrayString getLanguages() /*-{
		return this.Languages;
	}-*/;

	public final native void setLanguages(JsArrayString languages) /*-{
		this.Languages=languages;
	}-*/;
	
	public final native boolean getVisiblity() /*-{
		return this.Visible;
	}-*/;
	
	public final native void setVisiblity(boolean visible) /*-{
		this.Visible = visible;
	}-*/;

	public final native boolean getEnabled() /*-{
		return this.Enabled;
	}-*/;

	public final native void setEnabled(boolean enabled) /*-{
		this.Enabled = enabled;
	}-*/;
	

	public final native JsArrayString getAbbreviations() /*-{
		return this.Abbreviations;
	}-*/;
	
	public final native void setAbbreviations(JsArrayString abbreviations) /*-{
		this.Abbreviations=abbreviations;
	}-*/;
	
	public final native boolean isReadonlyField() /*-{
	return this.ReadonlyField;
}-*/;
}
