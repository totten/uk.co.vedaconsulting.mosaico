{crmScope extensionKey='uk.co.vedaconsulting.mosaico'}
<div class="crm-block crm-form-block crm-mosaico-form-block">
  {*<div class="help">*}
  {*{ts}...{/ts} {docURL page="Debugging for developers" resource="wiki"}*}
  {*</div>*}
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
  <table class="form-layout">
    <tr class="crm-mosaico-form-block-mosaico_layout">
      <td class="label">{$form.mosaico_layout.label}</td>
      <td>{$form.mosaico_layout.html}<br />
        <span class="description">{ts}How should the CiviMail composition screen look?{/ts}</span>
      </td>
    </tr>
  </table>
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
{/crmScope}
