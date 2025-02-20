import React, { useEffect, useState } from 'react';
import 'regenerator-runtime/runtime';
import './QuadsAdSellList.scss';

const {__} = wp.i18n;
const QuadsModifySellableRequestAd = (props) => {
    const [ad_image, setAdImage] = useState('');
    const [ad_data, setAdData] = useState({});
    const [is_saving, setIsSaving] = useState(false);
    let apiEndpoint = quads_localize_data.rest_url;
    // Fetch records from the custom table
    useEffect(() => {
        setAdData(props.data)
    }, []);
    const closeModal = () =>{
        props.handleCloseModal();
    }
    const handleClickUploadImage = () =>{
        document.getElementById('upload-ad-image').click();
    }
    const handleChangeUpload  = (event) =>{
        let adata = {...ad_data};
        var selectedFile = event.target.files[0];
        let url = window.URL.createObjectURL(selectedFile);
        setAdImage(url);
        adata['ad_image'] = url;
        setAdData(adata);
    }
    const handleChangeAdData = (value,key) =>{
        let adata = {...ad_data};
        adata[key] = value;
        setAdData(adata);
    }
    const handleSaveNewAdData = () =>{
        setIsSaving(true);
        let img_target = document.getElementById('upload-ad-image');
        let selectedFile  = img_target.files[0];
        let formData = new FormData();
        formData.append('action', 'quads_update_ad_request_data');
        formData.append('nonce', quads_localize_data.nonce);
        formData.append('ad_image', selectedFile);
        formData.append('ad_data', JSON.stringify(ad_data));

        fetch(ajaxurl,{
            method: "post",
            body: formData              
        })
        .then((res) => {
            setIsSaving(false);     
            props.handleModifyListData(ad_data); 
            closeModal();   
        })
        .then(
        (result) => {         
                               
        },        
        (error) => {
            
        }
        );  
    }
    return (
        <>
        {(ad_data.ad_content) &&
            <div className="quads-modal-popup">
                <div className="quads-modal-popup-content" style={{top:'unset'}}>
                    <span className="quads-modal-close" onClick={closeModal} style={{padding:'20px'}}>&times;</span>
                    <h3 style={{padding:'20px'}}>{__('Modify Ad Space Request', 'quick-adsense-reloaded')}</h3>
                    <div className="quads-modal-description"></div>
                    <div className="quads-modal-content adsforwp-quads-popup">
                        <div className="quads-modal">
                            <div>
                                <label>Enter Ad Text</label>
                                <input type='text' style={{width:'100%'}} placeholder='Enter Ad Text' value={ad_data.ad_content} onChange={(e)=>handleChangeAdData(e.target.value,'ad_content')}/>
                            </div>
                            <div style={{marginTop:'10px'}}>
                                <label>Enter Ad Link</label>
                                <input type='text' style={{width:'100%'}} placeholder='Enter Ad Link' value={ad_data.ad_link} onChange={(e)=>handleChangeAdData(e.target.value,'ad_link')}/>
                            </div>
                            {(ad_data.ad_image!=="") &&
                            <div style={{marginTop:'10px'}}>
                                <img src={ad_data.ad_image}  id="new_ad_image" style={{width:'150px'}}/>
                            </div>
                            }
                            {(ad_data.ad_image==="" && ad_image!=="") &&
                            <div style={{marginTop:'10px'}}>
                                <img src={ad_image}  id="new_ad_image" style={{width:'150px'}}/>
                            </div>
                            }
                            <input type='file' style={{display:'none'}} id="upload-ad-image" onChange={handleChangeUpload}/>
                            <button className='quads-btn quads-btn-primary btn-sm' style={{background: '#fff',color: '#005aef', border: '1px solid #005aef',padding:'5px 10px'}} onClick={handleClickUploadImage}>
                                {__('Upload Image', 'quick-adsense-reloaded')}
                            </button> 
                        </div>
                    </div>
                    <div className="quads-modal-popup-footer" style={{padding:'10px 20px', textAlign:'right'}}>
                        {(is_saving===false) &&
                            <button className='quads-btn quads-btn-primary' onClick={handleSaveNewAdData}>
                                {__('Save Changes', 'quick-adsense-reloaded')}
                            </button>       
                        }
                        {(is_saving===true) &&
                            <button className='quads-btn quads-btn-primary'>
                                {__('Saving Changes...', 'quick-adsense-reloaded')}
                            </button>       
                        }
                    </div>
                </div>
            </div>
            }
        </>
    );
};

export default QuadsModifySellableRequestAd;
