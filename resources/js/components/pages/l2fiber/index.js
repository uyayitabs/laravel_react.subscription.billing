import React from "react";
import classnames from "classnames";

import { Container, Row, Col, Card, ListGroup, ListGroupItem, TabContent, TabPane, Table } from "reactstrap";

import AddressUpdate from './AddressUpdates';
import RequestConnection from './RequestConnection';
import TerminateConnection from './TerminateConnection';
import AddressAvailability from './AddressAvailability';

class L2FiberServiceProvider extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            domain: 'domain-here',
            activeTab: '0',
            addresses: []
        };

        this.toggle = this.toggle.bind(this);

        this.tabs = [
            {
                title: 'Address Updates',
                component: <AddressUpdate domain={this.state.domain} />
            },
            {
                title: 'Request Connection',
                component: <RequestConnection domain={this.state.domain} />
            },
            {
                title: 'Terminate Connection',
                component: <TerminateConnection domain={this.state.domain} />
            },
            {
                title: 'Address Availability',
                component: <AddressAvailability domain={this.state.domain} />
            }
        ]
    }

    toggle(tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab });
        }
    }

    render() {
        return (
            <Container fluid className="p-0">
                <h1 className="h3 mb-3">L2Fiber Interface</h1>        
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
                                <TabPane 
                                tabId={ index.toString() }
                                >
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

export default L2FiberServiceProvider;
