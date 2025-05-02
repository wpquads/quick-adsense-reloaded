import React, { useState } from 'react';
import './QuadsAdModal.scss';
const QuadsUpgradeToProModal = ({ featureName, changePopupState }) => {
    const { __ } = wp.i18n;
    const hanldeClosePopup = () =>{
      changePopupState('',false);
    }
  return (
    <div className="gopropopup quads-modal-popup" style={{zIndex:99999}}>
      <div className="quads-modal-popup-content">
        <span className="quads-large-close" onClick={()=>hanldeClosePopup()}>&times;</span>

        <div className="quads-modal-popup-txt">
          <div className="quads-modal-popup-heading">
            {featureName} {__('is a PRO Feature', 'quick-adsense-reloaded')}
          </div>
          <p>
            {__("We're sorry, the " + featureName + " is not available on your plan. Please upgrade to the PRO plan to unlock all these awesome features.", 'quick-adsense-reloaded')}
          </p>
        </div>

        <div className="quads-modal-content">
          <a
            href="https://wpquads.com/pricing/#pricings"
            className="quads-got_pro premium_features_btn"
            target='_blank'
          >
            {__('Upgrade To PRO', 'quick-adsense-reloaded')}
          </a>
        </div>
      </div>
    </div>
  );
};

export default QuadsUpgradeToProModal;
