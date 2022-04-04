import React from "react";
import classnames from "classnames";

import { Container, Row, Col, Card, ListGroup, ListGroupItem, TabContent, TabPane } from "reactstrap";

class DetailsPage extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      activeTab: '0'
    }

    this.toggle = this.toggle.bind(this)
  }

  toggle(tab) {
    if (this.state.activeTab !== tab) {
      this.setState({ activeTab: tab })
    }
  }

  render() {
    const { name, tabs } = this.props

    return (
      <Container fluid className="p-0">
        <h3 className="mb-3">{ name }</h3>
        <Row>
          <Col md="3" xl="2">
            <Card>
              <ListGroup tabs="true">
                {
                  tabs.map((item, index) => {
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
                tabs.map((item, index) => {
                  return (
                    <React.Fragment key={ index }>
                      { this.state.activeTab.toString() === index.toString() ?                        
                        <TabPane tabId={ index.toString() } >
                          { item.component }
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

export default DetailsPage;
