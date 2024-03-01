import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Tabs from '@material-ui/core/Tabs';
import Tab from '@material-ui/core/Tab';
import Typography from '@material-ui/core/Typography';
import Box from '@material-ui/core/Box';
const {__} = wp.i18n;

const styles = (theme) => ({
  root: {
    flexGrow: 1,
    display: 'flex',
    height: 224,
  },
  tabs: {
    borderRight: `1px solid ${theme.palette.divider}`,
  },
});

class QuadsVerticalTabs extends Component {
  constructor(props) {
    super(props);
    this.state = {
      value: 0,
    };
  }

  handleChange = (event, newValue) => {
    this.setState({ value: newValue });
  };

  render() {
    const { classes } = this.props;
    const { value } = this.state;

    return (
      <div className={classes.root}>
        <Tabs
          orientation="vertical"
          variant="scrollable"
          value={value}
          onChange={this.handleChange}
          className={classes.tabs}
        >
           {this.props.RoleBasedAccess.length > 0 ? this.props.RoleBasedAccess.map((tab, index) => (
          <Tab label={tab.label} key={index}/>
                )) :'' }
        </Tabs>

        {
        this.props.RoleBasedAccess.length > 0 ? this.props.RoleBasedAccess.map((tab, index) => (
          <TabPanel value={value} index={index} key={index}>
          <label className="quads-switch">
            <input data-index={index} id={"setting_access_"+index} type="checkbox" onChange={this.props.handleCapabilityChange} defaultChecked={this.props.RoleBasedAccess[index]?.setting_access}/>
            <span id={"setting_access_"+index+"_"} className="quads-slider"></span>
          </label>
          {__(' Setting Access', 'quick-adsense-reloaded')}
          </TabPanel>
        )):''
      }
      </div>
    );
  }
}

QuadsVerticalTabs.propTypes = {
  classes: PropTypes.object.isRequired,
};

function TabPanel(props) {
  const { children, value, index } = props;

  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`vertical-tabpanel-${index}`}
      aria-labelledby={`vertical-tab-${index}`}
    >
      {value === index && (
        <Box p={3}>
          <Typography>{children}</Typography>
        </Box>
      )}
    </div>
  );
}

TabPanel.propTypes = {
  children: PropTypes.node,
  index: PropTypes.any.isRequired,
  value: PropTypes.any.isRequired,
};

export default withStyles(styles)(QuadsVerticalTabs);
