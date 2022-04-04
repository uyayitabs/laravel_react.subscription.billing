import React from "react";
import classnames from "classnames";

import { Card, Col, Container, ListGroup, ListGroupItem, Row, TabContent, TabPane } from "reactstrap";

import PublicInfo from './PublicInfo';
import PrivateInfo from './PrivateInfo';
import Security from './Security';

class Settings extends React.Component {

  constructor(props) {
    super(props)

    this.state = {
      activeTab: '0',
    }

    this.tabs = [
      {
        title: 'Public Info',
        component: <PublicInfo />
      }, {
        title: 'Private Info',
        component: <PrivateInfo />
      }, {
        title: 'Security',
        component: <Security />
      },
    ]

    this.toggle = this.toggle.bind(this)
  }

  toggle(tab) {
    if (this.state.activeTab !== tab) {
      this.setState({ activeTab: tab });
    }
  }

  render() {
    return(
      <Container fluid className="p-0">
        <h1 className="h3 mb-3">Settings</h1>

        <Row>
          <Col md="3" xl="2">
            <Card>
              <ListGroup tabs="true">
                {
                  this.tabs.map((item, index) => {
                    return (
                      <ListGroupItem 
                        key={ index }
                        className={ classnames({ active: this.state.activeTab === index.toString() }) }
                        onClick={() => { this.toggle(index.toString()) }}
                      >
                        { item.title }
                      </ListGroupItem>
                    )
                  })
                }
              </ListGroup>
            </Card>
          </Col>
          <Col md="9" xl="10">
            <TabContent activeTab={ this.state.activeTab }>
              {
                this.tabs.map((item, index) => {
                  return (
                      <React.Fragment key={ index }>
                      { this.state.activeTab.toString() === index.toString() ?                        
                          <TabPane tabId={ index.toString() } >
                            <Card>
                              { item.component }
                            </Card>
                          </TabPane> : null
                      }
                      </React.Fragment>
                  )
                })
              }              
            </TabContent>
          </Col>
        </Row>
      </Container>
    )
  }
}

export default Settings;
