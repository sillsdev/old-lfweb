package org.palaso.languageforge.client.lex.service.actions;

import org.palaso.languageforge.client.lex.common.BaseConfiguration;
import org.palaso.languageforge.client.lex.jsonrpc.JsonRpcAction;
import org.palaso.languageforge.client.lex.model.settings.inputsystems.SettingInputSystemsDto;

public class GetSettingInputSystemsAction extends JsonRpcAction<SettingInputSystemsDto> {

	public GetSettingInputSystemsAction() {
		super(BaseConfiguration.getInstance().getLFApiPath(), BaseConfiguration.getInstance().getApiFileName(), "getSettingInputSystems",0);
	}

	@Override
	public String encodeParam(int i) {
		return null;
	}

}