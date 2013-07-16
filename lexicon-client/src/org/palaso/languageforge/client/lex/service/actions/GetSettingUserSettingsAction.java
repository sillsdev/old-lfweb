package org.palaso.languageforge.client.lex.service.actions;

import org.palaso.languageforge.client.lex.common.BaseConfiguration;
import org.palaso.languageforge.client.lex.jsonrpc.JsonRpcAction;
import org.palaso.languageforge.client.lex.model.UserSettingsDto;

public class GetSettingUserSettingsAction extends
		JsonRpcAction<UserSettingsDto> {

	String value;

	public GetSettingUserSettingsAction(String userId) {
		super(BaseConfiguration.getInstance().getLFApiPath(), BaseConfiguration.getInstance().getApiFileName(), "getUserSettings", 1);
		value = userId;
	}

	@Override
	public String encodeParam(int i) {
		switch (i) {
		case 0:
			return  value;
		}
		return null;
	}
}
